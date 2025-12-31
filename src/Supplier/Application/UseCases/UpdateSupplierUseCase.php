<?php

namespace App\Supplier\Application\UseCases;

use App\Supplier\Application\DTO\UpdateSupplierRequest;
use App\Supplier\Application\DTO\UpdateSupplierResponse;
use App\Supplier\Application\InputPorts\UpdateSupplierUseCaseInterface;
use App\Supplier\Application\OutputPorts\Repositories\SupplierRepositoryInterface;
use Psr\Log\LoggerInterface;

class UpdateSupplierUseCase implements UpdateSupplierUseCaseInterface
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

    public function execute(UpdateSupplierRequest $request): UpdateSupplierResponse
    {
        try {
            // Find existing supplier
            $supplier = $this->supplierRepository->findById($request->getId());
            if (!$supplier) {
                return new UpdateSupplierResponse(null, 'SUPPLIER_NOT_FOUND', 404);
            }

            // Check if slug is being changed and if it's already taken by another supplier
            if ($supplier->getSlug() !== $request->getSlug()) {
                $existingSupplier = $this->supplierRepository->findBySlug($request->getSlug());
                if ($existingSupplier && $existingSupplier->getId() !== $request->getId()) {
                    return new UpdateSupplierResponse(null, 'SUPPLIER_SLUG_ALREADY_EXISTS', 400);
                }
            }

            // Update supplier entity
            $supplier->setName($request->getName());
            $supplier->setSlug($request->getSlug());
            $supplier->setLogoUrl($request->getLogoUrl());
            $supplier->setWebsite($request->getWebsite());
            $supplier->setCategory($request->getCategory());
            $supplier->setCountry($request->getCountry());
            $supplier->setCoverageArea($request->getCoverageArea());
            $supplier->setDescription($request->getDescription());
            $supplier->setHasApiIntegration($request->hasApiIntegration());
            $supplier->setIntegrationType($request->getIntegrationType());
            $supplier->setIsActive($request->isActive());
            $supplier->setFeatured($request->isFeatured());

            // Save supplier
            $this->supplierRepository->save($supplier);

            $supplierData = [
                'id' => $supplier->getId(),
                'name' => $supplier->getName(),
                'slug' => $supplier->getSlug(),
                'category' => $supplier->getCategory(),
                'country' => $supplier->getCountry(),
                'is_active' => $supplier->isActive(),
                'featured' => $supplier->isFeatured(),
            ];

            $this->logger->info('Supplier updated successfully', ['supplier_id' => $supplier->getId()]);

            return new UpdateSupplierResponse($supplierData, null, 200);

        } catch (\Exception $e) {
            $this->logger->error('Error updating supplier', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return new UpdateSupplierResponse(null, 'ERROR_UPDATING_SUPPLIER', 500);
        }
    }
}
