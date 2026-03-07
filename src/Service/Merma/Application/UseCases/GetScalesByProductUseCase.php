<?php

namespace App\Service\Merma\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Service\Merma\Application\DTO\GetScalesByProductRequest;
use App\Service\Merma\Application\DTO\GetScalesByProductResponse;
use App\Service\Merma\Application\InputPorts\GetScalesByProductUseCaseInterface;
use App\Service\Merma\Infrastructure\OutputAdapters\Repositories\ClientScalesRepository;
use Psr\Log\LoggerInterface;

final class GetScalesByProductUseCase implements GetScalesByProductUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager   $connectionManager,
        private readonly LoggerInterface           $logger,
    ) {}

    public function execute(GetScalesByProductRequest $request): GetScalesByProductResponse
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if ($client === null) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $em         = $this->connectionManager->getEntityManager($client->getUuidClient());
        $scalesRepo = new ClientScalesRepository($em);

        $scales = $scalesRepo->findAllByProductId($request->getProductId());

        $data = array_map(static function ($scale): array {
            return [
                'id'                 => $scale->getId(),
                'uuid_scale'         => $scale->getUuid(),
                'end_device_id'      => $scale->getEndDeviceId(),
                'active'             => $scale->isActive(),
                'voltage_percentage' => $scale->getVoltagePercentage(),
                'last_send'          => $scale->getLastSend()?->format(\DateTimeInterface::ATOM),
            ];
        }, $scales);

        $this->logger->info('ScalesByProduct retrieved', [
            'productId' => $request->getProductId(),
            'count'     => count($data),
        ]);

        return new GetScalesByProductResponse($data);
    }
}
