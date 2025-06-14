<?php

namespace App\Scales\Application\UseCases;

use App\Entity\Client\Scales;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\DTO\GetAllScalesRequest;
use App\Scales\Application\DTO\GetScaleResponse;
use App\Scales\Application\InputPorts\GetAllScalesUseCaseInterface;
use App\Scales\Infrastructure\OutputAdapters\Repositories\ScalesRepository;
use Psr\Log\LoggerInterface;

class GetAllScalesUseCase implements GetAllScalesUseCaseInterface
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;

    public function __construct(ClientConnectionManager $connectionManager, LoggerInterface $logger)
    {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
    }

    public function execute(GetAllScalesRequest $request): GetScaleResponse
    {
        $uuidClient = $request->getUuidClient();

        try {
            $em = $this->connectionManager->getEntityManager($uuidClient);
            $repo = new ScalesRepository($em);
            $scales = $repo->findAllByUuidClient($uuidClient);
            $data = array_map(fn(Scales $s) => $this->serializeScale($s), $scales);

            return new GetScaleResponse($data, null, 200);
        } catch (\Exception $e) {
            $this->logger->error('GetAllScalesUseCase: Error', ['exception' => $e]);
            return new GetScaleResponse(null, 'Internal Server Error', 500);
        }
    }

    private function serializeScale(Scales $scale): array
    {
        return [
            'uuid' => $scale->getUuid(),
            'end_device_id' => $scale->getEndDeviceId(),
            'product_id' => $scale->getProduct()?->getId(),
            'posX' => $scale->getPosX(),
            'width' => $scale->getWidth(),
        ];
    }
}
