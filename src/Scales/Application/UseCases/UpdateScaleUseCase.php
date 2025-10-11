<?php

namespace App\Scales\Application\UseCases;

use App\Entity\Client\ScaleHistory;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\DTO\UpdateScaleRequest;
use App\Scales\Application\DTO\UpdateScaleResponse;
use App\Scales\Application\InputPorts\UpdateScaleUseCaseInterface;
use App\Scales\Infrastructure\OutputAdapters\Repositories\ScalesRepository;
use Psr\Log\LoggerInterface;

class UpdateScaleUseCase implements UpdateScaleUseCaseInterface
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;

    public function __construct(ClientConnectionManager $connectionManager, LoggerInterface $logger)
    {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
    }

    public function execute(UpdateScaleRequest $request): UpdateScaleResponse
    {
        try {
            $em = $this->connectionManager->getEntityManager($request->getUuidClient());
            $repo = new ScalesRepository($em);
            $scale = $repo->findByUuidAndClient($request->getUuidScale(), $request->getUuidClient());
            if (!$scale) {
                return new UpdateScaleResponse(null, 'SCALE_NOT_FOUND', 404);
            }

            if (null !== $request->getProductId()) {
                $product = $em->getRepository('App\\Entity\\Client\\Product')->find($request->getProductId());
                $scale->setProduct($product);
            }
            if (null !== $request->getPosX()) {
                $scale->setPosX($request->getPosX());
            }
            if (null !== $request->getWidth()) {
                $scale->setWidth($request->getWidth());
            }

            $scale->setUuidUserModification($request->getUuidUserModification());
            $scale->setDatehourModification($request->getDatehourModification());

            $beforeData = ['uuid' => $scale->getUuid()];
            $afterData = ['uuid' => $scale->getUuid()];

            $history = new ScaleHistory();
            $history->setUuidScale($scale->getUuid());
            $history->setUuidUserModification($request->getUuidUserModification());
            $history->setDataScaleBeforeModification(json_encode($beforeData));
            $history->setDataScaleAfterModification(json_encode($afterData));
            $history->setDateModification(new \DateTime());

            $em->persist($history);
            $em->flush();

            $data = [
                'uuid' => $scale->getUuid(),
            ];

            return new UpdateScaleResponse($data, null, 200);
        } catch (\Exception $e) {
            $this->logger->error('UpdateScaleUseCase: Error', ['exception' => $e]);

            return new UpdateScaleResponse(null, 'Internal Server Error', 500);
        }
    }
}
