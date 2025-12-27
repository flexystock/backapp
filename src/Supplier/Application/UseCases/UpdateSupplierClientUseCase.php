<?php

namespace App\Supplier\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\ClientSupplier;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Supplier\Application\DTO\UpdateSupplierClientRequest;
use App\Supplier\Application\DTO\UpdateSupplierClientResponse;
use App\Supplier\Application\InputPorts\UpdateSupplierClientUseCaseInterface;
use App\Supplier\Infrastructure\OutputAdapters\Repositories\ClientSupplierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class UpdateSupplierClientUseCase implements UpdateSupplierClientUseCaseInterface
{
    private ClientRepositoryInterface $clientRepository;
    private ClientConnectionManager $connectionManager;
    private EntityManagerInterface $mainEntityManager;
    private LoggerInterface $logger;

    public function __construct(
        ClientRepositoryInterface $clientRepository,
        ClientConnectionManager $connectionManager,
        EntityManagerInterface $mainEntityManager,
        LoggerInterface $logger
    ) {
        $this->clientRepository = $clientRepository;
        $this->connectionManager = $connectionManager;
        $this->mainEntityManager = $mainEntityManager;
        $this->logger = $logger;
    }

    public function execute(UpdateSupplierClientRequest $request): UpdateSupplierClientResponse
    {
        try {
            // Find client
            $client = $this->clientRepository->findByUuid($request->getUuidClient());
            if (!$client) {
                return new UpdateSupplierClientResponse(null, 'CLIENT_NOT_FOUND', 404);
            }

            // Verify supplier exists in main database
            $supplier = $this->mainEntityManager->getRepository(\App\Entity\Main\Supplier::class)
                ->find($request->getSupplierId());
            
            if (!$supplier) {
                return new UpdateSupplierClientResponse(null, 'SUPPLIER_NOT_FOUND', 404);
            }

            // Get client-specific entity manager
            $clientEntityManager = $this->connectionManager->getEntityManager($client->getUuidClient());

            // Create repository with client entity manager
            $clientSupplierRepository = new ClientSupplierRepository($clientEntityManager);

            // Check if client supplier already exists
            $clientSupplier = $clientSupplierRepository->findBySupplierId($request->getSupplierId());

            if (!$clientSupplier) {
                // Create new client supplier
                $clientSupplier = new ClientSupplier();
                $clientSupplier->setSupplierId($request->getSupplierId());
            }

            // Update client supplier data
            $clientSupplier->setEmail($request->getEmail());
            $clientSupplier->setPhone($request->getPhone());
            $clientSupplier->setContactPerson($request->getContactPerson());
            $clientSupplier->setDeliveryDays($request->getDeliveryDays());
            $clientSupplier->setAddress($request->getAddress());
            $clientSupplier->setIntegrationEnabled($request->isIntegrationEnabled());
            $clientSupplier->setIntegrationConfig($request->getIntegrationConfig());
            $clientSupplier->setNotes($request->getNotes());
            $clientSupplier->setInternalCode($request->getInternalCode());
            $clientSupplier->setIsActive($request->isActive());
            $clientSupplier->setUpdatedAt(new \DateTime());

            // Save client supplier
            $clientSupplierRepository->save($clientSupplier);

            $supplierData = [
                'id' => $clientSupplier->getId(),
                'supplier_id' => $clientSupplier->getSupplierId(),
                'supplier_name' => $supplier->getName(),
                'email' => $clientSupplier->getEmail(),
                'phone' => $clientSupplier->getPhone(),
                'contact_person' => $clientSupplier->getContactPerson(),
                'delivery_days' => $clientSupplier->getDeliveryDays(),
                'address' => $clientSupplier->getAddress(),
                'integration_enabled' => $clientSupplier->isIntegrationEnabled(),
                'integration_config' => $clientSupplier->getIntegrationConfig(),
                'notes' => $clientSupplier->getNotes(),
                'internal_code' => $clientSupplier->getInternalCode(),
                'is_active' => $clientSupplier->isActive(),
                'created_at' => $clientSupplier->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $clientSupplier->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];

            $this->logger->info('Client supplier updated successfully', [
                'uuid_client' => $request->getUuidClient(),
                'supplier_id' => $request->getSupplierId()
            ]);

            return new UpdateSupplierClientResponse($supplierData, null, 200);

        } catch (\Exception $e) {
            $this->logger->error('Error updating client supplier', [
                'uuid_client' => $request->getUuidClient(),
                'supplier_id' => $request->getSupplierId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return new UpdateSupplierClientResponse(null, 'ERROR_UPDATING_CLIENT_SUPPLIER', 500);
        }
    }
}
