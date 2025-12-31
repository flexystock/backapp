<?php

namespace App\Supplier\Application\UseCases;

use App\Supplier\Application\DTO\GetAllSuppliersRequest;
use App\Supplier\Application\DTO\GetAllSuppliersResponse;
use App\Supplier\Application\InputPorts\GetAllSuppliersUseCaseInterface;
use App\Supplier\Application\OutputPorts\Repositories\SupplierRepositoryInterface;
use Psr\Log\LoggerInterface;

class GetAllSuppliersUseCase implements GetAllSuppliersUseCaseInterface
{
    private SupplierRepositoryInterface $supplierRepository;
    private LoggerInterface $logger;

    public function __construct(
        SupplierRepositoryInterface $supplierRepository,
        LoggerInterface $logger
    ) {
        $this->supplierRepository = $supplierRepository;
        $this->logger = $logger;
    }

    public function execute(GetAllSuppliersRequest $request): GetAllSuppliersResponse
    {
        try {
            $suppliers = $this->supplierRepository->findAll();

            $suppliersData = [];
            foreach ($suppliers as $supplier) {
                $suppliersData[] = [
                    'id' => $supplier->getId(),
                    'name' => $supplier->getName(),
                    'slug' => $supplier->getSlug(),
                    'logo_url' => $supplier->getLogoUrl(),
                    'website' => $supplier->getWebsite(),
                    'category' => $supplier->getCategory(),
                    'country' => $supplier->getCountry(),
                    'coverage_area' => $supplier->getCoverageArea(),
                    'description' => $supplier->getDescription(),
                    'has_api_integration' => $supplier->hasApiIntegration(),
                    'integration_type' => $supplier->getIntegrationType(),
                    'is_active' => $supplier->isActive(),
                    'featured' => $supplier->isFeatured(),
                    'created_at' => $supplier->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updated_at' => $supplier->getUpdatedAt()->format('Y-m-d H:i:s'),
                ];
            }

            $this->logger->info('Fetched all suppliers from main database', [
                'count' => count($suppliersData)
            ]);

            return new GetAllSuppliersResponse($suppliersData, null, 200);

        } catch (\Exception $e) {
            $this->logger->error('Error fetching all suppliers', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return new GetAllSuppliersResponse(null, 'ERROR_FETCHING_SUPPLIERS', 500);
        }
    }
}
