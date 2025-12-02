<?php

namespace App\Command;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\Report;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Message\GenerateScheduledReportMessage;
use App\Report\Application\OutputPorts\ReportExecutionRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:check-scheduled-reports',
    description: 'Verifica y encola los informes programados que deben ejecutarse'
)]
class CheckScheduledReportsCommand extends Command
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private ClientConnectionManager $connectionManager,
        private MessageBusInterface $messageBus,
        private LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Checking Scheduled Reports');

        $now = new \DateTime();
        $currentHour = (int) $now->format('H');
        $currentMinute = (int) $now->format('i');

        $this->logger->info('Starting scheduled reports check', [
            'current_time' => $now->format('Y-m-d H:i:s'),
        ]);

        // Obtener todos los clientes
        $clients = $this->clientRepository->findAll();
        $io->info(sprintf('Found %d clients to check', count($clients)));

        $totalEnqueued = 0;

        foreach ($clients as $client) {
            $uuidClient = $client->getUuidClient();

            try {
                // Cambiar al schema del cliente
                $clientEntityManager = $this->connectionManager->getEntityManager($uuidClient);

                // Obtener todos los informes del cliente
                $reports = $clientEntityManager->getRepository(Report::class)->findAll();

                $io->text(sprintf('Client %s: Found %d reports', $client->getName(), count($reports)));

                foreach ($reports as $report) {
                    $this->logger->info('Checking report', [
                        'report_id' => $report->getId(),
                        'report_name' => $report->getName(),
                        'send_time' => $report->getSendTime()->format('H:i:s'),
                        'period' => $report->getPeriod(),
                        'current_hour' => $currentHour,
                    ]);
                    if ($this->shouldExecuteReport($report, $clientEntityManager, $now, $currentHour, $currentMinute)) {
                        // Encolar mensaje
                        $message = new GenerateScheduledReportMessage($uuidClient, $report->getId());
                        $this->messageBus->dispatch($message);

                        $totalEnqueued++;

                        $io->success(sprintf(
                            'Enqueued report: %s (ID: %d) for client: %s',
                            $report->getName(),
                            $report->getId(),
                            $client->getName()
                        ));

                        $this->logger->info('Report enqueued', [
                            'uuid_client' => $uuidClient,
                            'report_id' => $report->getId(),
                            'report_name' => $report->getName(),
                        ]);
                    }
                }

            } catch (\Exception $e) {
                $io->error(sprintf(
                    'Error processing client %s: %s',
                    $client->getName(),
                    $e->getMessage()
                ));

                $this->logger->error('Error checking reports for client', [
                    'uuid_client' => $uuidClient,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $io->success(sprintf('Check completed. Total reports enqueued: %d', $totalEnqueued));

        return Command::SUCCESS;
    }

    /**
     * Determina si un informe debe ejecutarse en este momento
     */
    private function shouldExecuteReport(
        Report $report,
        object $clientEntityManager,
        \DateTime $now,
        int $currentHour,
        int $currentMinute
    ): bool {
        $sendTime = $report->getSendTime();
        $reportHour = (int) $sendTime->format('H');
        $reportMinute = (int) $sendTime->format('i');

        // Verificar si estamos en la hora correcta (con tolerancia de +/- 30 minutos)
        // Por ejemplo, si el informe es a las 14:05 y el cron corre cada hora a las 14:00,
        // el informe se ejecutará entre 14:00 y 14:59
        if ($currentHour !== $reportHour) {
            $this->logger->info('Report hour mismatch', [
                'report_id' => $report->getId(),
                'current_hour' => $currentHour,
                'report_hour' => $reportHour,
            ]);
            return false;
        }


        // Obtener el período de ejecución
        $period = $report->getPeriod();

        $this->logger->info('Report hour matches, checking period', [
            'report_id' => $report->getId(),
            'period' => $period,
        ]);

        // Verificar si ya se ejecutó en el período correspondiente
        return !$this->wasExecutedInPeriod($report, $clientEntityManager, $period, $now);
    }

    /**
     * Verifica si el informe ya se ejecutó en el período correspondiente
     */
    private function wasExecutedInPeriod(
        Report $report,
        object $clientEntityManager,
        string $period,
        \DateTime $now
    ): bool {
        $qb = $clientEntityManager->createQueryBuilder();

        // Determinar el rango de fechas según el período
        [$startDate, $endDate] = $this->getPeriodRange($period, $now);

        $count = $qb->select('COUNT(re.id)')
            ->from('App\Entity\Client\ReportExecution', 're')
            ->where('re.report = :report')
            ->andWhere('re.executedAt >= :startDate')
            ->andWhere('re.executedAt <= :endDate')
            ->andWhere('re.status = :status')
            ->setParameter('report', $report)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('status', 'success')
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $count > 0;
    }

    /**
     * Obtiene el rango de fechas según el período
     *
     * @return array{\DateTime, \DateTime}
     */
    private function getPeriodRange(string $period, \DateTime $now): array
    {
        $startDate = clone $now;
        $endDate = clone $now;

        switch ($period) {
            case 'daily':
                // Hoy desde las 00:00:00 hasta las 23:59:59
                $startDate->setTime(0, 0, 0);
                $endDate->setTime(23, 59, 59);
                break;

            case 'weekly':
                // Esta semana (desde el lunes 00:00:00 hasta el domingo 23:59:59)
                $startDate->modify('monday this week')->setTime(0, 0, 0);
                $endDate->modify('sunday this week')->setTime(23, 59, 59);
                break;

            case 'monthly':
                // Este mes (desde el día 1 00:00:00 hasta el último día 23:59:59)
                $startDate->modify('first day of this month')->setTime(0, 0, 0);
                $endDate->modify('last day of this month')->setTime(23, 59, 59);
                break;

            default:
                // Por defecto, hoy
                $startDate->setTime(0, 0, 0);
                $endDate->setTime(23, 59, 59);
                break;
        }

        return [$startDate, $endDate];
    }
}