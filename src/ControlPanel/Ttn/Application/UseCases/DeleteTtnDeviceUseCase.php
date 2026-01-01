<?php

declare(strict_types=1);

namespace App\ControlPanel\Ttn\Application\UseCases;

use App\ControlPanel\Ttn\Application\DTO\DeleteTtnDeviceRequest;
use App\ControlPanel\Ttn\Application\DTO\DeleteTtnDeviceResponse;
use App\ControlPanel\Ttn\Application\InputPorts\DeleteTtnDeviceUseCaseInterface;
use App\ControlPanel\Ttn\Application\OutputPorts\PoolScalesRepositoryInterface;
use App\ControlPanel\Ttn\Application\OutputPorts\PoolTtnDeviceRepositoryInterface;
use App\ControlPanel\Ttn\Application\OutputPorts\ScalesRepositoryInterface;
use App\ControlPanel\Ttn\Application\OutputPorts\TtnApiServiceInterface;
use Psr\Log\LoggerInterface;

class DeleteTtnDeviceUseCase implements DeleteTtnDeviceUseCaseInterface
{
    private LoggerInterface $logger;
    private PoolTtnDeviceRepositoryInterface $poolTtnDeviceRepository;
    private PoolScalesRepositoryInterface $poolScalesRepository;
    private ScalesRepositoryInterface $scalesRepository;
    private TtnApiServiceInterface $ttnApiService;

    public function __construct(
        LoggerInterface $logger,
        PoolTtnDeviceRepositoryInterface $poolTtnDeviceRepository,
        PoolScalesRepositoryInterface $poolScalesRepository,
        ScalesRepositoryInterface $scalesRepository,
        TtnApiServiceInterface $ttnApiService
    ) {
        $this->logger = $logger;
        $this->poolTtnDeviceRepository = $poolTtnDeviceRepository;
        $this->poolScalesRepository = $poolScalesRepository;
        $this->scalesRepository = $scalesRepository;
        $this->ttnApiService = $ttnApiService;
    }

    public function execute(DeleteTtnDeviceRequest $request): DeleteTtnDeviceResponse
    {
        $endDeviceId = $request->getEndDeviceId();

        $this->logger->info("Executing DeleteTtnDeviceUseCase for device: {$endDeviceId}");

        // Check if the device exists in pool_ttn_device
        $poolDevice = $this->poolTtnDeviceRepository->findOneByEndDeviceId($endDeviceId);
        if (!$poolDevice) {
            return new DeleteTtnDeviceResponse(
                false,
                'Device not found in pool_ttn_device',
                404
            );
        }

        // Check if the device is associated with a product in the scales table
        if ($this->scalesRepository->hasAssociatedProduct($endDeviceId)) {
            return new DeleteTtnDeviceResponse(
                false,
                'Cannot delete device. It is associated with a product. Please disassociate it first.',
                400
            );
        }

        // Delete from TTN API
        $ttnDeleted = $this->ttnApiService->deleteDevice($endDeviceId);
        if (!$ttnDeleted) {
            return new DeleteTtnDeviceResponse(
                false,
                'Failed to delete device from TTN network',
                500
            );
        }

        // Delete from pool_ttn_device (main database)
        $this->poolTtnDeviceRepository->delete($poolDevice);

        // Delete from pool_scales (client database) if exists
        $poolScale = $this->poolScalesRepository->findOneByEndDeviceId($endDeviceId);
        if ($poolScale) {
            $this->poolScalesRepository->delete($poolScale);
        }

        $this->logger->info("Successfully deleted device: {$endDeviceId}");

        return new DeleteTtnDeviceResponse(
            true,
            'Device deleted successfully',
            200
        );
    }
}
