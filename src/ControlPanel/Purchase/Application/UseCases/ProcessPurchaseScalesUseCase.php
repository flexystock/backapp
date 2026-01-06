<?php

declare(strict_types=1);

namespace App\ControlPanel\Purchase\Application\UseCases;

use App\ControlPanel\Purchase\Application\DTO\ProcessPurchaseScalesRequest;
use App\ControlPanel\Purchase\Application\DTO\ProcessPurchaseScalesResponse;
use App\ControlPanel\Purchase\Application\InputPorts\ProcessPurchaseScalesUseCaseInterface;
use App\ControlPanel\Purchase\Application\OutputPorts\ClientRepositoryInterface;
use App\ControlPanel\Purchase\Application\OutputPorts\EmailProcessPurchaseScalesServiceInterface;
use App\ControlPanel\Purchase\Application\OutputPorts\PurchaseScalesRepositoryInterface;
use App\Ttn\Application\DTO\RegisterTtnDeviceRequest;
use App\Ttn\Application\InputPorts\RegisterTtnDeviceUseCaseInterface;
use DateTimeImmutable;

class ProcessPurchaseScalesUseCase implements ProcessPurchaseScalesUseCaseInterface
{
    private PurchaseScalesRepositoryInterface $purchaseScalesRepository;
    private ClientRepositoryInterface $clientRepository;
    private RegisterTtnDeviceUseCaseInterface $registerTtnDeviceUseCase;
    private EmailProcessPurchaseScalesServiceInterface $emailService;

    public function __construct(
        PurchaseScalesRepositoryInterface $purchaseScalesRepository,
        ClientRepositoryInterface $clientRepository,
        RegisterTtnDeviceUseCaseInterface $registerTtnDeviceUseCase,
        EmailProcessPurchaseScalesServiceInterface $emailService
    ) {
        $this->purchaseScalesRepository = $purchaseScalesRepository;
        $this->clientRepository = $clientRepository;
        $this->registerTtnDeviceUseCase = $registerTtnDeviceUseCase;
        $this->emailService = $emailService;
    }

    public function execute(ProcessPurchaseScalesRequest $request): ProcessPurchaseScalesResponse
    {
        try {
            // 1. Find the purchase
            $purchase = $this->purchaseScalesRepository->findByUuidPurchase($request->getUuidPurchase());
            
            if (!$purchase) {
                return new ProcessPurchaseScalesResponse(
                    false,
                    'Purchase not found'
                );
            }

            // 2. Validate purchase status is pending
            if ($purchase->getStatus() !== 'pending') {
                return new ProcessPurchaseScalesResponse(
                    false,
                    'Purchase is not in pending status. Current status: ' . $purchase->getStatus()
                );
            }

            // 3. Get client information
            $client = $this->clientRepository->findByUuid($purchase->getUuidClient());
            
            if (!$client) {
                return new ProcessPurchaseScalesResponse(
                    false,
                    'Client not found'
                );
            }

            // 4. Create devices based on quantity
            $quantity = $purchase->getQuantity();
            $devicesCreated = [];
            $currentDateTime = new DateTimeImmutable();

            for ($i = 0; $i < $quantity; $i++) {
                // Create request for RegisterTtnDeviceUseCase
                $registerRequest = new RegisterTtnDeviceRequest(
                    $request->getUuidUser(),
                    $currentDateTime,
                    $purchase->getUuidClient(),
                    null, // devEui - will be auto-generated
                    null, // joinEui - will be auto-generated
                    null, // appKey - will be auto-generated
                    null  // deviceId - will be auto-generated
                );

                // Register device using existing use case
                $registerResponse = $this->registerTtnDeviceUseCase->execute($registerRequest);

                if (!$registerResponse->isSuccess()) {
                    // If any device fails, return error
                    return new ProcessPurchaseScalesResponse(
                        false,
                        'Failed to create device ' . ($i + 1) . ' of ' . $quantity . ': ' . $registerResponse->getError(),
                        $devicesCreated
                    );
                }

                // Note: We don't have the device ID in the response, but it's created successfully
                // You may need to modify RegisterTtnDeviceResponse to return the device ID
                // For now, we'll indicate success without the specific device ID
            }

            // 5. Update purchase status to processing
            $purchase->setStatus('processing');
            $purchase->setProcessedAt(new DateTimeImmutable());
            $purchase->setProcessedByUuidUser($request->getUuidUser());
            
            $this->purchaseScalesRepository->save($purchase);

            // 6. Send email notification to client
            $this->emailService->sendScalesProcessingNotificationToClient($client, $purchase);

            // 7. Return success
            return new ProcessPurchaseScalesResponse(
                true,
                sprintf('Purchase processed successfully. %d device(s) created.', $quantity),
                $devicesCreated
            );
        } catch (\Exception $e) {
            return new ProcessPurchaseScalesResponse(
                false,
                'Error processing purchase: ' . $e->getMessage()
            );
        }
    }
}
