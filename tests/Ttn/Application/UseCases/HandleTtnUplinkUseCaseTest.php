<?php

namespace App\Tests\Ttn\Application\UseCases;

use App\Entity\Client\Product;
use App\Entity\Client\Scales;
use App\Entity\Client\WeightsLog;
use App\Entity\Main\PoolTtnDevice;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\OutputPorts\ScalesRepositoryInterface;
use App\Ttn\Application\DTO\TtnUplinkRequest;
use App\Ttn\Application\OutputPorts\MinimumStockNotificationInterface;
use App\Ttn\Application\OutputPorts\PoolTtnDeviceRepositoryInterface;
use App\Ttn\Application\OutputPorts\WeightVariationAlertNotifierInterface;
use App\Ttn\Application\UseCases\HandleTtnUplinkUseCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class HandleTtnUplinkUseCaseTest extends TestCase
{
    private PoolTtnDeviceRepositoryInterface $poolTtnDeviceRepository;
    private ClientConnectionManager $connectionManager;
    private ScalesRepositoryInterface $scaleRepository;
    private LoggerInterface $logger;
    private EntityManagerInterface $mainEntityManager;
    private MinimumStockNotificationInterface $minimumStockNotifier;
    private WeightVariationAlertNotifierInterface $weightVariationNotifier;
    private HandleTtnUplinkUseCase $useCase;

    protected function setUp(): void
    {
        $this->poolTtnDeviceRepository = $this->getMockBuilder(PoolTtnDeviceRepositoryInterface::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $this->connectionManager = $this->createMock(ClientConnectionManager::class);
        $this->scaleRepository = $this->createMock(ScalesRepositoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->mainEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->minimumStockNotifier = $this->createMock(MinimumStockNotificationInterface::class);
        $this->weightVariationNotifier = $this->createMock(WeightVariationAlertNotifierInterface::class);

        $this->useCase = new HandleTtnUplinkUseCase(
            $this->poolTtnDeviceRepository,
            $this->connectionManager,
            $this->scaleRepository,
            $this->logger,
            $this->mainEntityManager,
            $this->minimumStockNotifier,
            $this->weightVariationNotifier
        );
    }

    public function testTareIsSubtractedFromGrossWeightForFirstReading(): void
    {
        // Arrange
        $deviceId = 'test-device-123';
        $devEui = 'test-eui-456';
        $uuidClient = 'client-uuid-789';
        $grossWeightGrams = 5000; // 5 kg gross weight
        $tareGrams = 1500; // 1500 grams tare (already in grams in DB)
        $expectedNetWeightGrams = 3500; // 5000 - 1500 = 3500 grams
        $expectedNetWeightKg = 3.5; // 3.5 kg

        $request = new TtnUplinkRequest(
            $devEui,
            $deviceId,
            null, // joinEui
            3.5,
            null, // weight (will be calculated)
            $grossWeightGrams
        );

        // Mock TTN device
        $ttnDevice = $this->createMock(PoolTtnDevice::class);
        $ttnDevice->method('getEndDeviceName')->willReturn($uuidClient);
        $this->poolTtnDeviceRepository->method('findOneBy')->willReturn($ttnDevice);

        // Mock entity manager
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->connectionManager->method('getEntityManager')->willReturn($entityManager);

        // Mock scale
        $scale = $this->createMock(Scales::class);
        $scale->method('getId')->willReturn(1);

        // Mock product with tare (in grams)
        $product = $this->createMock(Product::class);
        $product->method('getTare')->willReturn($tareGrams);
        $product->method('getWeightRange')->willReturn(100.0);
        $product->method('getMainUnit')->willReturn('0');

        $scale->method('getProduct')->willReturn($product);

        // Mock repositories
        $scaleRepository = $this->createMock(EntityRepository::class);
        $scaleRepository->method('findOneBy')->willReturn($scale);

        $weightsLogRepo = $this->createMock(EntityRepository::class);
        $weightsLogRepo->method('findOneBy')->willReturn(null); // No previous log

        $clientConfigRepo = $this->createMock(EntityRepository::class);
        $clientConfigRepo->method('findOneBy')->willReturn(null);

        $entityManager->method('getRepository')->willReturnCallback(
            function ($class) use ($scaleRepository, $weightsLogRepo, $clientConfigRepo) {
                if ($class === Scales::class) {
                    return $scaleRepository;
                }
                if ($class === WeightsLog::class) {
                    return $weightsLogRepo;
                }
                return $clientConfigRepo;
            }
        );

        // Capture the weight log that gets persisted
        $capturedWeightLog = null;
        $entityManager->expects($this->atLeastOnce())
            ->method('persist')
            ->willReturnCallback(function ($entity) use (&$capturedWeightLog) {
                if ($entity instanceof WeightsLog) {
                    $capturedWeightLog = $entity;
                }
            });

        // Act
        $this->useCase->execute($request);

        // Assert
        $this->assertNotNull($capturedWeightLog, 'WeightsLog should have been persisted');
        $this->assertEquals(
            $expectedNetWeightGrams,
            $capturedWeightLog->getWeightGrams(),
            'Net weight in grams should equal gross weight minus tare'
        );
        $this->assertEquals(
            $expectedNetWeightKg,
            $capturedWeightLog->getRealWeight(),
            'Real weight in kg should equal net weight'
        );
    }

    public function testTareIsSubtractedForSignificantWeightChange(): void
    {
        // Arrange
        $deviceId = 'test-device-123';
        $devEui = 'test-eui-456';
        $uuidClient = 'client-uuid-789';
        
        // Previous reading: 5000g gross (with 1500g tare = 3500g net)
        $previousGrossWeightGrams = 5000;
        $tareGrams = 1500; // Tare in grams (already in grams in DB)
        $previousNetWeightGrams = 3500;
        $previousNetWeightKg = 3.5;
        
        // New reading: 6000g gross (with 1500g tare = 4500g net)
        $newGrossWeightGrams = 6000;
        $expectedNewNetWeightGrams = 4500;
        $expectedNewNetWeightKg = 4.5;
        
        $weightRange = 100.0; // Threshold for detecting changes

        $request = new TtnUplinkRequest(
            $devEui,
            $deviceId,
            null, // joinEui
            3.5,
            null, // weight (will be calculated)
            $newGrossWeightGrams
        );

        // Mock TTN device
        $ttnDevice = $this->createMock(PoolTtnDevice::class);
        $ttnDevice->method('getEndDeviceName')->willReturn($uuidClient);
        $this->poolTtnDeviceRepository->method('findOneBy')->willReturn($ttnDevice);

        // Mock entity manager
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->connectionManager->method('getEntityManager')->willReturn($entityManager);

        // Mock scale
        $scale = $this->createMock(Scales::class);
        $scale->method('getId')->willReturn(1);

        // Mock product with tare (in grams)
        $product = $this->createMock(Product::class);
        $product->method('getTare')->willReturn($tareGrams);
        $product->method('getWeightRange')->willReturn($weightRange);
        $product->method('getMainUnit')->willReturn('0');
        $product->method('getId')->willReturn(1);
        $product->method('getName')->willReturn('Test Product');

        $scale->method('getProduct')->willReturn($product);

        // Mock previous weight log
        $previousLog = $this->createMock(WeightsLog::class);
        $previousLog->method('getWeightGrams')->willReturn($previousNetWeightGrams);
        $previousLog->method('getRealWeight')->willReturn($previousNetWeightKg);
        $previousLog->method('getId')->willReturn(1);

        // Mock repositories
        $scaleRepository = $this->createMock(EntityRepository::class);
        $scaleRepository->method('findOneBy')->willReturn($scale);

        $weightsLogRepo = $this->createMock(EntityRepository::class);
        $weightsLogRepo->method('findOneBy')->willReturn($previousLog);

        $clientConfigRepo = $this->createMock(EntityRepository::class);
        $clientConfigRepo->method('findOneBy')->willReturn(null);

        $businessHourRepo = $this->createMock(EntityRepository::class);
        $businessHourRepo->method('findBy')->willReturn([]);

        $entityManager->method('getRepository')->willReturnCallback(
            function ($class) use ($scaleRepository, $weightsLogRepo, $clientConfigRepo, $businessHourRepo) {
                if ($class === Scales::class) {
                    return $scaleRepository;
                }
                if ($class === WeightsLog::class) {
                    return $weightsLogRepo;
                }
                if (str_contains($class, 'BusinessHour')) {
                    return $businessHourRepo;
                }
                return $clientConfigRepo;
            }
        );

        // Capture the new weight log
        $capturedWeightLog = null;
        $entityManager->expects($this->atLeastOnce())
            ->method('persist')
            ->willReturnCallback(function ($entity) use (&$capturedWeightLog) {
                if ($entity instanceof WeightsLog) {
                    $capturedWeightLog = $entity;
                }
            });

        // Act
        $this->useCase->execute($request);

        // Assert
        $this->assertNotNull($capturedWeightLog, 'New WeightsLog should have been persisted');
        $this->assertEquals(
            $expectedNewNetWeightGrams,
            $capturedWeightLog->getWeightGrams(),
            'New net weight in grams should equal new gross weight minus tare'
        );
        $this->assertEquals(
            $expectedNewNetWeightKg,
            $capturedWeightLog->getRealWeight(),
            'Real weight should be updated correctly with tare considered'
        );
    }

    public function testZeroTareDoesNotAffectWeight(): void
    {
        // Arrange
        $deviceId = 'test-device-123';
        $devEui = 'test-eui-456';
        $uuidClient = 'client-uuid-789';
        $weightGrams = 5000; // 5 kg
        $tareGrams = 0.0; // No tare (in grams)

        $request = new TtnUplinkRequest(
            $devEui,
            $deviceId,
            null, // joinEui
            3.5,
            null, // weight (will be calculated)
            $weightGrams
        );

        // Mock TTN device
        $ttnDevice = $this->createMock(PoolTtnDevice::class);
        $ttnDevice->method('getEndDeviceName')->willReturn($uuidClient);
        $this->poolTtnDeviceRepository->method('findOneBy')->willReturn($ttnDevice);

        // Mock entity manager
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->connectionManager->method('getEntityManager')->willReturn($entityManager);

        // Mock scale
        $scale = $this->createMock(Scales::class);
        $scale->method('getId')->willReturn(1);

        // Mock product with zero tare (in grams)
        $product = $this->createMock(Product::class);
        $product->method('getTare')->willReturn($tareGrams);
        $product->method('getWeightRange')->willReturn(100.0);
        $product->method('getMainUnit')->willReturn('0');

        $scale->method('getProduct')->willReturn($product);

        // Mock repositories
        $scaleRepository = $this->createMock(EntityRepository::class);
        $scaleRepository->method('findOneBy')->willReturn($scale);

        $weightsLogRepo = $this->createMock(EntityRepository::class);
        $weightsLogRepo->method('findOneBy')->willReturn(null);

        $clientConfigRepo = $this->createMock(EntityRepository::class);
        $clientConfigRepo->method('findOneBy')->willReturn(null);

        $entityManager->method('getRepository')->willReturnCallback(
            function ($class) use ($scaleRepository, $weightsLogRepo, $clientConfigRepo) {
                if ($class === Scales::class) {
                    return $scaleRepository;
                }
                if ($class === WeightsLog::class) {
                    return $weightsLogRepo;
                }
                return $clientConfigRepo;
            }
        );

        // Capture the weight log
        $capturedWeightLog = null;
        $entityManager->expects($this->atLeastOnce())
            ->method('persist')
            ->willReturnCallback(function ($entity) use (&$capturedWeightLog) {
                if ($entity instanceof WeightsLog) {
                    $capturedWeightLog = $entity;
                }
            });

        // Act
        $this->useCase->execute($request);

        // Assert
        $this->assertNotNull($capturedWeightLog, 'WeightsLog should have been persisted');
        $this->assertEquals(
            $weightGrams,
            $capturedWeightLog->getWeightGrams(),
            'With zero tare, weight should remain unchanged'
        );
        $this->assertEquals(
            5.0,
            $capturedWeightLog->getRealWeight(),
            'Real weight should equal the gross weight when tare is zero'
        );
    }
}
