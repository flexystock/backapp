<?php

namespace App\Scales\Application\UseCases;

use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\DTO\UnassignScaleFromProductRequest;
use App\Scales\Application\DTO\UnassignScaleFromProductResponse;
use App\Scales\Application\InputPorts\UnassignScaleFromProductUseCaseInterface;
use App\Scales\Application\OutputPorts\PoolScalesRepositoryInterface;
use App\Scales\Application\OutputPorts\ScalesRepositoryInterface;
use App\Scales\Infrastructure\OutputAdapters\Repositories\PoolScalesRepository;
use App\Scales\Infrastructure\OutputAdapters\Repositories\ScalesRepository;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class UnassignScaleFromProductUseCase implements UnassignScaleFromProductUseCaseInterface
{
    private ClientConnectionManager $clientConnectionManager;
    private PoolScalesRepositoryInterface $poolScalesRepository;
    private ScalesRepositoryInterface $scalesRepository;
    private LoggerInterface $logger;

    public function __construct(
        ClientConnectionManager $clientConnectionManager,
        PoolScalesRepositoryInterface $poolScalesRepository,
        ScalesRepositoryInterface $scalesRepository,
        LoggerInterface $logger
    ) {
        $this->clientConnectionManager = $clientConnectionManager;
        $this->poolScalesRepository = $poolScalesRepository;
        $this->scalesRepository = $scalesRepository;
        $this->logger = $logger;
    }
    public function execute(UnassignScaleFromProductRequest $request): UnassignScaleFromProductResponse
    {
        $uuidClient = $request->getUuidClient();
        $endDeviceId = $request->getEndDeviceId();
        if (!$uuidClient || !$endDeviceId) {
            return new UnassignScaleFromProductResponse(null, 'uuidClient and uuidSacle are required', 400);
        }

        try {
            $em = $this->clientConnectionManager->getEntityManager($request->getUuidClient());
            // ---------- LOG IMPORTANTE ----------
            $connection = $em->getConnection();
            $this->logger->info('DEBUG CLIENT DB', [
                'uuidClient' => $uuidClient,
                'dbname'     => $connection->getDatabase(),
            ]);
            // -------------------------------------
            $repo = new ScalesRepository($em);

            // Check if the scale exists
            $uuidScale = $repo->findUuidByEndDeviceId($endDeviceId);
            $scale = $repo->findByUuid($uuidScale);
            if (!$scale) {
                return new UnassignScaleFromProductResponse(null, 'SCALE_NOT_FOUND', 404);
            }

            $this->logger->info('DEBUG CLIENT DB', [
                'uuidScale' => $uuidScale,
                '$scale'     => $scale,
            ]);
            // Unassign the scale from the product
            $scale->setProduct(null);
            $scale->setUuidUserModification($request->getUuidUser());
            $scale->setDatehourModification(new DateTime());
            $repo->save($scale);

            // Update the pool scale availability
            $pool = new PoolScalesRepository($em);
            $poolScale = $pool->findOneByUuidScale($uuidScale);
            $this->logger->info('DEBUG CLIENT DB ANTES ', [
                'poolsacle' => $pool,
                'uuidScale'     => $uuidScale,
                'available' => $poolScale->isAvailable(),
            ]);
            if ($poolScale) {
                $poolScale->setAvailable(TRUE);
                $poolScale->setUuidUserModification($request->getUuidUser());
                $poolScale->setDatehourModification(new DateTime());
                $pool->savePoolScale($poolScale);
            }

            return new UnassignScaleFromProductResponse(['status' => 'SCALE_UNASSIGNED_SUCCESSFULLY'], null, 200);
        } catch (\Exception $e) {
            $this->logger->error('UnassignScaleFromProductUseCase: Error', ['exception' => $e]);
            return new UnassignScaleFromProductResponse(null, 'Internal Server Error', 500);
        }

    }
}