<?php

namespace App\Scales\Application\UseCases;

use App\Entity\Main\User;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\DTO\DeleteScaleRequest;
use App\Scales\Application\DTO\DeleteScaleResponse;
use App\Scales\Application\InputPorts\DeleteScaleUseCaseInterface;
use App\Scales\Infrastructure\OutputAdapters\Repositories\ScalesRepository;
use Psr\Log\LoggerInterface;

class DeleteScaleUseCase implements DeleteScaleUseCaseInterface
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;

    public function __construct(ClientConnectionManager $connectionManager, LoggerInterface $logger)
    {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
    }

    public function execute(DeleteScaleRequest $request, User $user): DeleteScaleResponse
    {
        $uuidClient = $request->getUuidClient();
        $uuidScale = $request->getUuidScale();

        try {
            $em = $this->connectionManager->getEntityManager($uuidClient);
            $repo = new ScalesRepository($em);
            $scale = $repo->findByUuidAndClient($uuidScale, $uuidClient);
            if (!$scale) {
                return new DeleteScaleResponse(null, 'SCALE_NOT_FOUND', 404);
            }
            $repo->remove($scale);

            return new DeleteScaleResponse('SCALE_DELETED_SUCCESSFULLY', null, 200);
        } catch (\Exception $e) {
            $this->logger->error('DeleteScaleUseCase: Error', ['exception' => $e]);

            return new DeleteScaleResponse(null, 'Internal Server Error', 500);
        }
    }
}
