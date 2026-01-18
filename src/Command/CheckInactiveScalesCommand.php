<?php

namespace App\Command;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'app:check-inactive-scales',
    description: 'Verifica si hay balanzas sin actualización en los últimos 5 minutos'
)]
class CheckInactiveScalesCommand extends Command
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private ClientConnectionManager $connectionManager,
        private MailerInterface $mailer,
        private LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Checking Inactive Scales');

        $now = new \DateTime();

        $this->logger->info('Starting inactive scales check', [
            'current_time' => $now->format('Y-m-d H:i:s'),
        ]);

        // Obtener todos los clientes
        $clients = $this->clientRepository->findAll();
        $io->info(sprintf('Found %d clients to check', count($clients)));

        $inactiveScales = [];

        foreach ($clients as $client) {
            $uuidClient = $client->getUuidClient();

            try {
                // Cambiar al schema del cliente
                $clientEntityManager = $this->connectionManager->getEntityManager($uuidClient);

                // Buscar balanzas inactivas (más de 5 minutos sin actualizar)
                $sql = "
                    SELECT 
                        s.id, 
                        s.uuid_scale,
                        s.end_device_id, 
                        s.last_send,
                        s.voltage_percentage,
                        s.battery_die,
                        p.name as product_name,
                        TIMESTAMPDIFF(MINUTE, DATE_ADD(s.last_send, INTERVAL -1 HOUR), UTC_TIMESTAMP()) as minutes_ago
                    FROM scales s
                    LEFT JOIN products p ON s.product_id = p.id
                    WHERE s.last_send IS NOT NULL 
                    AND DATE_ADD(s.last_send, INTERVAL -1 HOUR) < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 5 MINUTE)
                    AND s.active = 1
                ";

                $connection = $clientEntityManager->getConnection();
                $scales = $connection->fetchAllAssociative($sql);

                $io->text(sprintf(
                    'Client %s: Found %d inactive scales',
                    $client->getName(),
                    count($scales)
                ));

                if (!empty($scales)) {
                    $inactiveScales[] = [
                        'client_name' => $client->getName(),
                        'uuid_client' => $uuidClient,
                        'scales' => $scales
                    ];

                    $this->logger->warning('Inactive scales found', [
                        'uuid_client' => $uuidClient,
                        'client_name' => $client->getName(),
                        'inactive_count' => count($scales),
                    ]);

                    $io->warning(sprintf(
                        'Found %d inactive scales in client: %s',
                        count($scales),
                        $client->getName()
                    ));
                }

            } catch (\Exception $e) {
                $io->error(sprintf(
                    'Error processing client %s: %s',
                    $client->getName(),
                    $e->getMessage()
                ));

                $this->logger->error('Error checking scales for client', [
                    'uuid_client' => $uuidClient,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Si hay balanzas inactivas, enviar email
        if (!empty($inactiveScales)) {
            try {
                $this->sendAlertEmail($inactiveScales);
                $totalScales = $this->countTotalScales($inactiveScales);

                $io->success(sprintf(
                    'Alert email sent with %d inactive scales',
                    $totalScales
                ));

                $this->logger->info('Alert email sent', [
                    'total_inactive_scales' => $totalScales,
                    'affected_clients' => count($inactiveScales),
                ]);
            } catch (\Exception $e) {
                $io->error('Error sending alert email: ' . $e->getMessage());

                $this->logger->error('Error sending alert email', [
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            $io->success('All scales are active');

            $this->logger->info('All scales are active');
        }

        return Command::SUCCESS;
    }

    private function sendAlertEmail(array $inactiveScales): void
    {
        $totalScales = $this->countTotalScales($inactiveScales);
        $htmlBody = $this->buildEmailBody($inactiveScales);

        $email = (new Email())
            ->from('flexystock@gmail.com')
            ->to('flexystock@gmail.com') // Cambia esto a tu email
            ->subject("⚠️ Alerta: {$totalScales} balanzas inactivas detectadas")
            ->html($htmlBody);

        $this->mailer->send($email);
    }

    private function buildEmailBody(array $inactiveScales): string
    {
        $html = '<h2>🚨 Balanzas Inactivas - FlexyStock</h2>';
        $html .= '<p>Las siguientes balanzas llevan más de 5 minutos sin enviar datos:</p>';

        foreach ($inactiveScales as $data) {
            $html .= "<h3>Cliente: {$data['client_name']} (UUID: {$data['uuid_client']})</h3>";
            $html .= '<table border="1" cellpadding="10" style="border-collapse: collapse; width: 100%;">';
            $html .= '<tr style="background-color: #f0f0f0;">
                        <th>ID</th>
                        <th>UUID</th>
                        <th>Device ID (TTN)</th>
                        <th>Producto</th>
                        <th>Batería</th>
                        <th>Última actualización</th>
                        <th>Inactiva desde hace</th>
                      </tr>';

            foreach ($data['scales'] as $scale) {
                $batteryInfo = $scale['voltage_percentage'] . '%';
                if ($scale['battery_die']) {
                    $batteryInfo .= ' (Est. fin: ' . $scale['battery_die'] . ')';
                }

                $productName = $scale['product_name'] ?? 'Sin producto';

                $html .= "<tr>
                            <td>{$scale['id']}</td>
                            <td style='font-size: 10px;'>{$scale['uuid_scale']}</td>
                            <td><strong>{$scale['end_device_id']}</strong></td>
                            <td>{$productName}</td>
                            <td>{$batteryInfo}</td>
                            <td>{$scale['last_send']}</td>
                            <td><strong style='color: red;'>{$scale['minutes_ago']} minutos</strong></td>
                          </tr>";
            }

            $html .= '</table><br>';
        }

        $html .= '<p style="color: #666; font-size: 12px;">Este es un email automático generado por el sistema de monitoreo de FlexyStock.</p>';

        return $html;
    }

    private function countTotalScales(array $inactiveScales): int
    {
        $total = 0;
        foreach ($inactiveScales as $data) {
            $total += count($data['scales']);
        }
        return $total;
    }
}