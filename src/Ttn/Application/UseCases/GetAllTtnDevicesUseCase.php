<?php

namespace App\Ttn\Application\UseCases;

use App\Entity\Main\PoolTtnDevice;
use App\Ttn\Application\DTO\GetAllTtnDevicesResponse;
use App\Ttn\Application\InputPorts\GetAllTtnDevicesUseCaseInterface;
use App\Ttn\Application\OutputPorts\PoolTtnDeviceRepositoryInterface;
use App\Ttn\Application\OutputPorts\TtnServiceInterface;
use Knp\Component\Pager\PaginatorInterface;

class GetAllTtnDevicesUseCase implements GetAllTtnDevicesUseCaseInterface
{
    private PoolTtnDeviceRepositoryInterface $poolTtnDeviceRepository;
    private TtnServiceInterface $ttnService;
    private PaginatorInterface $paginator;

    public function __construct(PoolTtnDeviceRepositoryInterface $poolTtnDeviceRepository, TtnServiceInterface $ttnService,
        PaginatorInterface $paginator)
    {
        $this->poolTtnDeviceRepository = $poolTtnDeviceRepository;
        $this->ttnService = $ttnService;
        $this->paginator = $paginator;
    }

    public function execute(): GetAllTtnDevicesResponse
    {
        $ttnDevices = $this->poolTtnDeviceRepository->getAll();

        $devices = array_map(function (PoolTtnDevice $p) {
            return $this->serializeTtnDevice($p);
        }, $ttnDevices);

        return new GetAllTtnDevicesResponse(true, null, $devices);
    }

    public function getAll(): array
    {
        // TODO: Implement getAll() method.
    }

    public function executePaginated(int $page, int $limit, ?bool $available): GetAllTtnDevicesResponse
    {
        // 1. Obtenemos QueryBuilder en lugar de traer todo
        $qb = $this->poolTtnDeviceRepository->createQueryBuilderAllDevices($available);

        // 2. Paginamos
        $pagination = $this->paginator->paginate(
            $qb,    // Query | QueryBuilder | array
            $page,
            $limit
        );

        // 3. Convertimos cada item en array
        $devicesArray = [];
        foreach ($pagination->getItems() as $p) {
            // Aseguramos que $p sea una instancia de PoolTtnDevice
            if ($p instanceof PoolTtnDevice) {
                $devicesArray[] = $this->serializeTtnDevice($p);
            }
        }

        // 4. Retornamos la estructura que necesitemos
        return new GetAllTtnDevicesResponse(
            true,
            null,
            $devicesArray,
            [
                'totalItems' => $pagination->getTotalItemCount(),
                'itemsPerPage' => $limit,
                'currentPage' => $page,
            ]
        );
    }

    /**
     * Mapea un PoolTtnDevice a array con los campos que quieras exponer.
     */
    private function serializeTtnDevice(PoolTtnDevice $ttnDevice): array
    {
        return [
            'uuid' => $ttnDevice->getUuidUserCreation(),
            'available' => $ttnDevice->getAvailable(),
            'end_device_id' => $ttnDevice->getEndDeviceId(),
            'app_eui' => $ttnDevice->getAppEUI(),
            'dev_eui' => $ttnDevice->getDevEUI(),
            'app_key' => $ttnDevice->getAppKey(),
            // ...otros campos que necesites
        ];
    }
}
