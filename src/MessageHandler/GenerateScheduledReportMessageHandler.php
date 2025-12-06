<?php

namespace App\MessageHandler;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\ReportExecution;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Message\GenerateScheduledReportMessage;
use App\Report\Application\DTO\GenerateReportNowRequest;
use App\Report\Application\InputPorts\GenerateReportNowUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: GenerateScheduledReportMessage::class)]
class GenerateScheduledReportMessageHandler
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private ClientConnectionManager $connectionManager,
        private GenerateReportNowUseCaseInterface $generateReportUseCase,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(GenerateScheduledReportMessage $message): void
    {
        $this->logger->info('=== Starting scheduled report generation ===', [
            'tenant_id' => $message->getTenantId(),
            'report_id' => $message->getReportId(),
        ]);

        try {
            // 1. Buscar el cliente/tenant
            $client = $this->clientRepository->findOneBy(['uuid_client' => $message->getTenantId()]);

            if (!$client) {
                $this->logger->warning('Client not found', ['tenant_id' => $message->getTenantId()]);
                return;
            }

            $uuidClient = $client->getUuidClient();

            // 2. Cambiar al schema del cliente
            $clientEntityManager = $this->connectionManager->getEntityManager($uuidClient);

            // 3. Buscar el informe
            $reportRepository = $clientEntityManager->getRepository('App\Entity\Client\Report');
            $report = $reportRepository->find($message->getReportId());

            if (!$report) {
                $this->logger->warning('Report not found', ['report_id' => $message->getReportId()]);
                return;
            }

            // 4. Crear registro de ejecución
            $execution = new ReportExecution();
            $execution->setReport($report);
            $execution->setExecutedAt(new \DateTime());
            $execution->setStatus('processing');
            $execution->setSended(false);

            $clientEntityManager->persist($execution);
            $clientEntityManager->flush();

            $this->logger->info('Execution record created', ['execution_id' => $execution->getId()]);

            // 5. NUEVO: Extraer productIds si el filtro es 'specific'
            $productIds = [];
            if ($report->hasSpecificProducts()) {
                $productIds = $report->getProductIds();
                $this->logger->info('Report has specific products', [
                    'product_count' => count($productIds),
                    'product_ids' => $productIds,
                ]);
            }

            // 6. Crear request con el constructor correcto
            $generateRequest = new GenerateReportNowRequest(
                uuidClient: $uuidClient,
                name: $report->getName(),
                reportType: $report->getReportType(),
                productFilter: $report->getProductFilter() ?? 'all',
                email: $report->getEmail(),
                period: $report->getPeriod(),
                productIds: $productIds // NUEVO: Pasar los productIds
            );

            // Establecer los campos opcionales
            $generateRequest->setUuidUser($report->getUuidUserCreation());
            $generateRequest->setTimestamp(new \DateTimeImmutable());

            // 7. Generar y enviar el informe
            $response = $this->generateReportUseCase->execute($generateRequest);

            // 8. Actualizar el estado de la ejecución
            if ($response->isSuccess()) {
                $execution->setStatus('success');
                $execution->setSended(true);
                $this->logger->info('✅ Scheduled report generated and sent successfully', [
                    'products_count' => $response->getData()['products_count'] ?? 0,
                ]);
            } else {
                $execution->setStatus('failed');
                $execution->setErrorMessage($response->getMessage());
                $this->logger->error('Report generation failed', ['message' => $response->getMessage()]);
            }

            $clientEntityManager->flush();

        } catch (\Exception $e) {
            $this->logger->error('Error generating scheduled report', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Si existe el registro de ejecución, marcarlo como fallido
            if (isset($execution) && isset($clientEntityManager)) {
                try {
                    $execution->setStatus('failed');
                    $execution->setErrorMessage($e->getMessage());
                    $clientEntityManager->flush();
                } catch (\Exception $innerException) {
                    $this->logger->error('Failed to update execution status', [
                        'error' => $innerException->getMessage(),
                    ]);
                }
            }

            throw $e;
        }
    }
}