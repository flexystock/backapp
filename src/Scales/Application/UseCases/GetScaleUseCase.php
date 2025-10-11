<?php

namespace App\Scales\Application\UseCases;

use App\Entity\Client\Scales;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\DTO\GetScaleRequest;
use App\Scales\Application\DTO\GetScaleResponse;
use App\Scales\Application\InputPorts\GetScaleUseCaseInterface;
use App\Scales\Infrastructure\OutputAdapters\Repositories\ScalesRepository;
use Psr\Log\LoggerInterface;

class GetScaleUseCase implements GetScaleUseCaseInterface
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;

    public function __construct(ClientConnectionManager $connectionManager, LoggerInterface $logger)
    {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
    }

    public function execute(GetScaleRequest $request): GetScaleResponse
    {
        $uuidClient = $request->getUuidClient();
        $uuidScale = $request->getUuidScale();

        try {
            $em = $this->connectionManager->getEntityManager($uuidClient);
            $repo = new ScalesRepository($em);
            $scale = $repo->findByUuidAndClient($uuidScale, $uuidClient);
            if (!$scale) {
                return new GetScaleResponse(null, 'SCALE_NOT_FOUND', 404);
            }
            $data = $this->serializeScale($scale);

            return new GetScaleResponse($data, null, 200);
        } catch (\Exception $e) {
            $this->logger->error('GetScaleUseCase: Error', ['exception' => $e]);

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
