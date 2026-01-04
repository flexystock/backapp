<?php

declare(strict_types=1);

namespace App\Scales\Application\UseCases;

use App\Entity\Main\PurchaseScales;
use App\Scales\Application\DTO\PurchaseScalesRequest;
use App\Scales\Application\DTO\PurchaseScalesResponse;
use App\Scales\Application\InputPorts\PurchaseScalesUseCaseInterface;
use App\Scales\Application\OutputPorts\EmailPurchaseScalesServiceInterface;
use App\Scales\Application\OutputPorts\PurchaseScalesRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class PurchaseScalesUseCase implements PurchaseScalesUseCaseInterface
{
    private PurchaseScalesRepositoryInterface $purchaseScalesRepository;
    private EmailPurchaseScalesServiceInterface $emailService;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(
        PurchaseScalesRepositoryInterface $purchaseScalesRepository,
        EmailPurchaseScalesServiceInterface $emailService,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->purchaseScalesRepository = $purchaseScalesRepository;
        $this->emailService = $emailService;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function execute(PurchaseScalesRequest $request): PurchaseScalesResponse
    {
        try {
            $this->logger->info('Executing PurchaseScalesUseCase', [
                'uuidClient' => $request->getUuidClient(),
                'numScales' => $request->getNumScales(),
            ]);

            // Get client information
            $client = $this->entityManager->getRepository(\App\Entity\Main\Client::class)
                ->findOneBy(['uuid_client' => $request->getUuidClient()]);

            if (!$client) {
                return new PurchaseScalesResponse(
                    false,
                    'Client not found',
                    null,
                    404
                );
            }

            // Create purchase request
            $purchaseScales = new PurchaseScales();
            $purchaseScales->setUuidClient($request->getUuidClient());
            $purchaseScales->setClientName($client->getName());
            $purchaseScales->setQuantity($request->getNumScales());
            $purchaseScales->setStatus('pending');

            // Save to database
            $this->purchaseScalesRepository->save($purchaseScales);

            // Send notification email to Flexystock
            try {
                $this->emailService->sendPurchaseNotificationToFlexystock($purchaseScales);
                $this->logger->info('Purchase notification email sent to Flexystock', [
                    'uuidPurchase' => $purchaseScales->getUuidPurchase(),
                ]);
            } catch (\Exception $e) {
                $this->logger->error('Failed to send purchase notification email', [
                    'error' => $e->getMessage(),
                    'uuidPurchase' => $purchaseScales->getUuidPurchase(),
                ]);
                // Continue even if email fails
            }

            return new PurchaseScalesResponse(
                true,
                'Scale purchase request created successfully',
                $purchaseScales->getUuidPurchase(),
                201
            );
        } catch (\Exception $e) {
            $this->logger->error('Error in PurchaseScalesUseCase', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return new PurchaseScalesResponse(
                false,
                'An error occurred while processing the request',
                null,
                500
            );
        }
    }
}
