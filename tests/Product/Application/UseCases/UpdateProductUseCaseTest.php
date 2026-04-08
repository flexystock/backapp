<?php

namespace App\Tests\Product\Application\UseCases;

use App\Entity\Client\Product;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Product\Application\DTO\UpdateProductRequest;
use App\Product\Application\UseCases\UpdateProductUseCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class UpdateProductUseCaseTest extends TestCase
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private UpdateProductUseCase $useCase;

    protected function setUp(): void
    {
        $this->connectionManager = $this->createMock(ClientConnectionManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->useCase = new UpdateProductUseCase(
            $this->connectionManager,
            $this->logger
        );
    }

    private function buildRequest(
        string $uuidClient = 'c014a415-4113-49e5-80cb-cc3158c15236',
        string $uuidProduct = '9a6ae1c0-3bc6-41c8-975a-4de5b4357666',
        ?int $mainUnit = 0,
    ): UpdateProductRequest {
        $request = new UpdateProductRequest(
            $uuidClient,
            $uuidProduct,
            'Test Product',
            null,
            null,
            false,
            0.00,
            null,
            null,
            null,
            null,
            null,
            $mainUnit,
        );
        $request->setUuidUserModification('user-uuid-123');
        $request->setDatehourModification(new \DateTime());

        return $request;
    }

    public function testExecuteWithValidRequestReturnsSuccessResponse(): void
    {
        $product = $this->createMock(Product::class);
        $product->method('getUuid')->willReturn('9a6ae1c0-3bc6-41c8-975a-4de5b4357666');
        $product->method('getName')->willReturn('Test Product');
        $product->method('getEan')->willReturn(null);
        $product->method('getExpirationDate')->willReturn(null);
        $product->method('getPerishable')->willReturn(false);
        $product->method('getStock')->willReturn(0.0);
        $product->method('getWeightRange')->willReturn(null);
        $product->method('getWeightUnit1')->willReturn(null);
        $product->method('getNameUnit1')->willReturn(null);
        $product->method('getWeightUnit2')->willReturn(null);
        $product->method('getNameUnit2')->willReturn(null);
        $product->method('getMainUnit')->willReturn('0');
        $product->method('getTare')->willReturn(0.0);
        $product->method('getSalePrice')->willReturn(0.0);
        $product->method('getCostPrice')->willReturn(0.0);
        $product->method('getOutSystemStock')->willReturn(null);
        $product->method('getDaysAverageConsumption')->willReturn(30);
        $product->method('getDaysServeOrder')->willReturn(0);

        $doctrineRepository = $this->createMock(EntityRepository::class);
        $doctrineRepository->method('findOneBy')->willReturn($product);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturn($doctrineRepository);

        $this->connectionManager->method('getEntityManager')->willReturn($em);

        $request = $this->buildRequest();
        $response = $this->useCase->execute($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertIsArray($response->getProduct());
        $this->assertSame('9a6ae1c0-3bc6-41c8-975a-4de5b4357666', $response->getProduct()['uuid']);
    }

    public function testProductNotFoundReturns404(): void
    {
        $doctrineRepository = $this->createMock(EntityRepository::class);
        $doctrineRepository->method('findOneBy')->willReturn(null);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturn($doctrineRepository);

        $this->connectionManager->method('getEntityManager')->willReturn($em);

        $this->logger->expects($this->once())->method('warning');

        $request = $this->buildRequest();
        $response = $this->useCase->execute($request);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('PRODUCT_NOT_FOUND', $response->getError());
    }

    public function testExecuteCastsMainUnitToStringBeforePassingToEntity(): void
    {
        $product = $this->createMock(Product::class);
        $product->method('getUuid')->willReturn('9a6ae1c0-3bc6-41c8-975a-4de5b4357666');
        $product->method('getName')->willReturn('Test Product');
        $product->method('getEan')->willReturn(null);
        $product->method('getExpirationDate')->willReturn(null);
        $product->method('getPerishable')->willReturn(false);
        $product->method('getStock')->willReturn(0.0);
        $product->method('getWeightRange')->willReturn(null);
        $product->method('getWeightUnit1')->willReturn(null);
        $product->method('getNameUnit1')->willReturn(null);
        $product->method('getWeightUnit2')->willReturn(null);
        $product->method('getNameUnit2')->willReturn(null);
        $product->method('getMainUnit')->willReturn('1');
        $product->method('getTare')->willReturn(0.0);
        $product->method('getSalePrice')->willReturn(0.0);
        $product->method('getCostPrice')->willReturn(0.0);
        $product->method('getOutSystemStock')->willReturn(null);
        $product->method('getDaysAverageConsumption')->willReturn(30);
        $product->method('getDaysServeOrder')->willReturn(0);

        // Assert that setMainUnit is called with the string "1", not the integer 1
        $product->expects($this->once())
            ->method('setMainUnit')
            ->with($this->identicalTo('1'));

        $doctrineRepository = $this->createMock(EntityRepository::class);
        $doctrineRepository->method('findOneBy')->willReturn($product);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturn($doctrineRepository);

        $this->connectionManager->method('getEntityManager')->willReturn($em);

        $request = $this->buildRequest(mainUnit: 1);
        $response = $this->useCase->execute($request);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testExecuteCatchesThrowableAndReturnsInternalServerError(): void
    {
        $product = $this->createMock(Product::class);
        $product->method('getUuid')->willReturn('9a6ae1c0-3bc6-41c8-975a-4de5b4357666');
        $product->method('getName')->willReturn('Test Product');
        $product->method('getEan')->willReturn(null);
        $product->method('getExpirationDate')->willReturn(null);
        $product->method('getPerishable')->willReturn(false);
        $product->method('getStock')->willReturn(0.0);
        $product->method('getWeightRange')->willReturn(null);
        $product->method('getWeightUnit1')->willReturn(null);
        $product->method('getNameUnit1')->willReturn(null);
        $product->method('getWeightUnit2')->willReturn(null);
        $product->method('getNameUnit2')->willReturn(null);
        $product->method('getMainUnit')->willReturn('0');
        $product->method('getTare')->willReturn(0.0);
        $product->method('getSalePrice')->willReturn(0.0);
        $product->method('getCostPrice')->willReturn(0.0);
        $product->method('getOutSystemStock')->willReturn(null);
        $product->method('getDaysAverageConsumption')->willReturn(30);
        $product->method('getDaysServeOrder')->willReturn(0);

        $doctrineRepository = $this->createMock(EntityRepository::class);
        $doctrineRepository->method('findOneBy')->willReturn($product);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturn($doctrineRepository);
        $em->method('flush')->willThrowException(new \TypeError('Simulated TypeError'));

        $this->connectionManager->method('getEntityManager')->willReturn($em);

        $this->logger->expects($this->once())->method('error');

        $request = $this->buildRequest();
        $response = $this->useCase->execute($request);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame('Internal Server Error', $response->getError());
    }

    public function testMissingUuidClientThrowsRuntimeException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('CLIENT_NOT_FOUND');

        $request = $this->buildRequest(uuidClient: '');
        $this->useCase->execute($request);
    }

    public function testMissingUuidProductThrowsRuntimeException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('PRODUCT_NOT_FOUND');

        $request = $this->buildRequest(uuidProduct: '');
        $this->useCase->execute($request);
    }
}
