<?php

namespace App\Supplier\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Supplier\Application\DTO\GetAllSuppliersClientRequest;
use App\Supplier\Application\DTO\GetAllSuppliersClientResponse;
use App\Supplier\Application\InputPorts\GetAllSuppliersClientUseCaseInterface;
use App\Supplier\Application\OutputPorts\Repositories\ClientSupplierRepositoryInterface;
use App\Supplier\Infrastructure\OutputAdapters\Repositories\ClientSupplierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class GetAllSuppliersClientUseCase implements GetAllSuppliersClientUseCaseInterface
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

    public function execute(GetAllSuppliersClientRequest $request): GetAllSuppliersClientResponse
    {
        try {
            // Find client
            $client = $this->clientRepository->findByUuid($request->getUuidClient());
            if (!$client) {
                return new GetAllSuppliersClientResponse(null, 'CLIENT_NOT_FOUND', 404);
            }

            // Get client-specific entity manager
            $clientEntityManager = $this->connectionManager->getEntityManager($client->getUuidClient());

            // Create repository with client entity manager
            $clientSupplierRepository = new ClientSupplierRepository($clientEntityManager);

            // Fetch all client suppliers
            $clientSuppliers = $clientSupplierRepository->findAll();

            $suppliersData = [];
            foreach ($clientSuppliers as $clientSupplier) {
                // Fetch supplier name from main database
                $supplierName = null;
                $supplier = $this->mainEntityManager->getRepository(\App\Entity\Main\Supplier::class)
                    ->find($clientSupplier->getSupplierId());
                
                if ($supplier) {
                    $supplierName = $supplier->getName();
                }

                $suppliersData[] = [
                    'id' => $clientSupplier->getId(),
                    'supplier_id' => $clientSupplier->getSupplierId(),
                    'supplier_name' => $supplierName,
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
            }

            $this->logger->info('Fetched all client suppliers', [
                'uuid_client' => $request->getUuidClient(),
                'count' => count($suppliersData)
            ]);

            return new GetAllSuppliersClientResponse($suppliersData, null, 200);

        } catch (\Exception $e) {
            $this->logger->error('Error fetching client suppliers', [
                'uuid_client' => $request->getUuidClient(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return new GetAllSuppliersClientResponse(null, 'ERROR_FETCHING_CLIENT_SUPPLIERS', 500);
        }
    }
}
