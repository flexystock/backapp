<?php

namespace App\Service\Merma\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\Product;
use App\Entity\Client\ProductServiceHour;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Service\Merma\Application\DTO\UpdateProductServiceHoursRequest;
use App\Service\Merma\Application\InputPorts\UpdateProductServiceHoursUseCaseInterface;
use App\Service\Merma\Infrastructure\OutputAdapters\Repositories\ClientProductServiceHourRepository;
use Psr\Log\LoggerInterface;

final class UpdateProductServiceHoursUseCase implements UpdateProductServiceHoursUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager   $connectionManager,
        private readonly LoggerInterface           $logger,
    ) {}

    public function execute(UpdateProductServiceHoursRequest $request): void
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if ($client === null) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $em         = $this->connectionManager->getEntityManager($client->getUuidClient());
        $hourRepo   = new ClientProductServiceHourRepository($em);
        $productId  = $request->getProductId();

        $product = $em->getRepository(Product::class)->find($productId);
        if ($product === null) {
            throw new \RuntimeException("PRODUCT_NOT_FOUND:{$productId}");
        }

        $hourRepo->deleteByProductId($productId);

        foreach ($request->getHours() as $h) {
            $hour = new ProductServiceHour();
            $hour->setProduct($product)
                 ->setDayOfWeek($h['dayOfWeek'])
                 ->setStartTime1(new \DateTime($h['startTime1']))
                 ->setEndTime1(new \DateTime($h['endTime1']))
                 ->setStartTime2(isset($h['startTime2']) ? new \DateTime($h['startTime2']) : null)
                 ->setEndTime2(isset($h['endTime2']) ? new \DateTime($h['endTime2']) : null);

            $hourRepo->save($hour);
        }

        $this->logger->info('ProductServiceHours updated', [
            'productId' => $productId,
        ]);
    }
}
