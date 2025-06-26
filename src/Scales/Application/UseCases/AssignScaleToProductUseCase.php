<?php

namespace App\Scales\Application\UseCases;

use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\DTO\AssignScaleToProductRequest;
use App\Scales\Application\DTO\AssignScaleToProductResponse;
use App\Scales\Application\InputPorts\AssignScaleToProductUseCaseInterface;
use App\Scales\Infrastructure\OutputAdapters\Repositories\PoolScalesRepository;
use App\Scales\Infrastructure\OutputAdapters\Repositories\ScalesRepository;
use App\Entity\Client\Scales as ScaleEntity;
use Psr\Log\LoggerInterface;

class AssignScaleToProductUseCase implements AssignScaleToProductUseCaseInterface
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    public function __construct(ClientConnectionManager $connectionManager, LoggerInterface $logger)
    {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
    }

    public function execute(AssignScaleToProductRequest $request): AssignScaleToProductResponse
    {
        try {
            $em = $this->connectionManager->getEntityManager($request->getUuidClient());

            $poolRepo = new PoolScalesRepository($em);
            $scaleRepo = new ScalesRepository($em);
            //var_dump($poolRepo);
            //die();
            $poolScale = $poolRepo->findOneBy($request->getEndDeviceId());
            if (!$poolScale) {
                return new AssignScaleToProductResponse(false, 'POOL_SCALE_NOT_FOUND');
            }

            $product = $em->getRepository('App\\Entity\\Client\\Product')->find($request->getProductId());
            if (!$product) {
                return new AssignScaleToProductResponse(false, 'PRODUCT_NOT_FOUND');
            }

            $poolScale->setAvailable(false);
            $poolRepo->savePoolScale($poolScale);

            $scale = $scaleRepo->findOneBy($request->getEndDeviceId());
            if (!$scale) {
                $scale = new ScaleEntity();
                $scale->setUuid($poolScale->getUuid());
                $scale->setEndDeviceId($request->getEndDeviceId());
                $scale->setVoltageMin(3.2);
                $scale->setVoltagePercentage(0);
                $scale->setUuidUserCreation($request->getUuidUserCreation());
                $scale->setDatehourCreation(new \DateTime());
            }

            $scale->setProduct($product);

            $scaleRepo->save($scale);

            return new AssignScaleToProductResponse(true);
        } catch (\Exception $e) {
            $this->logger->error('AssignScaleToProductUseCase: Error', ['exception' => $e]);

            return new AssignScaleToProductResponse(false, 'Internal Server Error');
        }
    }
}
