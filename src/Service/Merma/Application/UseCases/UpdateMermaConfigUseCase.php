<?php

namespace App\Service\Merma\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Service\Merma\Application\DTO\UpdateMermaConfigRequest;
use App\Service\Merma\Application\InputPorts\UpdateMermaConfigUseCaseInterface;
use App\Service\Merma\Infrastructure\OutputAdapters\Repositories\ClientMermaConfigRepository;
use Psr\Log\LoggerInterface;

final class UpdateMermaConfigUseCase implements UpdateMermaConfigUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager   $connectionManager,
        private readonly LoggerInterface           $logger,
    ) {}

    public function execute(UpdateMermaConfigRequest $request): array
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if ($client === null) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $em         = $this->connectionManager->getEntityManager($client->getUuidClient());
        $configRepo = new ClientMermaConfigRepository($em);

        $config = $configRepo->findByProductId($request->getProductId());

        if ($config === null) {
            $config = $configRepo->createDefaultForProduct($request->getProductId());
        }

        try {
            $serviceStart = \DateTime::createFromFormat('H:i', $request->getServiceStart());
            $serviceEnd   = \DateTime::createFromFormat('H:i', $request->getServiceEnd());
            if ($serviceStart === false || $serviceEnd === false) {
                throw new \RuntimeException('INVALID_TIME_FORMAT');
            }
        } catch (\Exception) {
            throw new \RuntimeException('INVALID_TIME_FORMAT');
        }

        $config->setRendimientoEsperadoPct($request->getRendimientoEsperadoPct())
               ->setServiceStart($serviceStart)
               ->setServiceEnd($serviceEnd)
               ->setAnomalyThresholdKg($request->getAnomalyThresholdKg())
               ->setAlertOnAnomaly($request->isAlertOnAnomaly());

        $configRepo->save($config);

        $this->logger->info('MermaConfig updated', [
            'productId' => $request->getProductId(),
        ]);

        return [
            'id'                      => $config->getId(),
            'product_id'              => $config->getProduct()->getId(),
            'rendimiento_esperado_pct' => $config->getRendimientoEsperadoPct(),
            'service_start'           => $config->getServiceStart()->format('H:i'),
            'service_end'             => $config->getServiceEnd()->format('H:i'),
            'anomaly_threshold_kg'    => $config->getAnomalyThresholdKg(),
            'alert_on_anomaly'        => $config->isAlertOnAnomaly(),
            'created_at'              => $config->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at'              => $config->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
