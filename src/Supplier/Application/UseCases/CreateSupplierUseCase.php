<?php

namespace App\Supplier\Application\UseCases;

use App\Entity\Main\Supplier;
use App\Supplier\Application\DTO\CreateSupplierRequest;
use App\Supplier\Application\DTO\CreateSupplierResponse;
use App\Supplier\Application\InputPorts\CreateSupplierUseCaseInterface;
use App\Supplier\Application\OutputPorts\Repositories\SupplierRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateSupplierUseCase implements CreateSupplierUseCaseInterface
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

    public function execute(CreateSupplierRequest $request): CreateSupplierResponse
    {
        try {
            // Check if slug already exists
            $existingSupplier = $this->supplierRepository->findBySlug($request->getSlug());
            if ($existingSupplier) {
                return new CreateSupplierResponse(null, 'SUPPLIER_SLUG_ALREADY_EXISTS', 400);
            }

            // Create supplier entity
            $supplier = new Supplier();
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
            ];

            $this->logger->info('Supplier created successfully', ['supplier_id' => $supplier->getId()]);

            return new CreateSupplierResponse($supplierData, null, 201);

        } catch (\Exception $e) {
            $this->logger->error('Error creating supplier', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return new CreateSupplierResponse(null, 'ERROR_CREATING_SUPPLIER', 500);
        }
    }
}
