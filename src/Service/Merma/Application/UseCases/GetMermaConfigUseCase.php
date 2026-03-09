<?php

namespace App\Service\Merma\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Service\Merma\Application\DTO\GetMermaConfigRequest;
use App\Service\Merma\Application\InputPorts\GetMermaConfigUseCaseInterface;
use App\Service\Merma\Infrastructure\OutputAdapters\Repositories\ClientMermaConfigRepository;
use Psr\Log\LoggerInterface;

final class GetMermaConfigUseCase implements GetMermaConfigUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager   $connectionManager,
        private readonly LoggerInterface           $logger,
    ) {}

    public function execute(GetMermaConfigRequest $request): array
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if ($client === null) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $em         = $this->connectionManager->getEntityManager($client->getUuidClient());
        $configRepo = new ClientMermaConfigRepository($em);

        $config = $configRepo->findByProductId($request->getProductId());

        if ($config === null) {
            throw new \RuntimeException('MERMA_CONFIG_NOT_FOUND');
        }

        $this->logger->info('MermaConfig retrieved', [
            'productId' => $request->getProductId(),
        ]);

        return [
            'id'                      => $config->getId(),
            'product_id'              => $config->getProduct()->getId(),
            'rendimiento_esperado_pct' => $config->getRendimientoEsperadoPct(),
            'alert_on_anomaly'        => $config->isAlertOnAnomaly(),
            'created_at'              => $config->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at'              => $config->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
