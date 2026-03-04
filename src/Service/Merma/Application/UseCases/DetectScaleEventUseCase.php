<?php

namespace App\Service\Merma\Application\UseCases;

use App\Entity\Client\ScaleEvent;
use App\Service\Merma\Application\DTO\ScaleEventResultDTO;
use App\Service\Merma\Application\DTO\ScaleReadingDTO;
use App\Service\Merma\Application\InputPorts\MermaEventDetectorInterface;
use App\Service\Merma\Application\OutputPorts\MermaConfigRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\MermaNotifierInterface;
use App\Service\Merma\Application\OutputPorts\ScaleEventRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\ScaleReadingRepositoryInterface;
use App\Service\Merma\ScaleEventDetectorService;
use Psr\Log\LoggerInterface;

/**
 * DetectScaleEventUseCase
 *
 * Orquesta la detección de eventos a partir de una nueva lectura de peso.
 * Implementa MermaEventDetectorInterface → es el contrato que usa el InputAdapter TTN.
 *
 * Flujo:
 *   1. Obtiene el peso anterior de la balanza
 *   2. Delega la clasificación en ScaleEventDetectorService (lógica de dominio pura)
 *   3. Persiste el ScaleEvent resultante
 *   4. Dispara la alerta si es anomalía
 *   5. Devuelve el DTO de resultado al InputAdapter
 */
final class DetectScaleEventUseCase implements MermaEventDetectorInterface
{
    public function __construct(
        private readonly ScaleEventDetectorService         $detector,
        private readonly ScaleEventRepositoryInterface     $eventRepo,
        private readonly ScaleReadingRepositoryInterface   $readingRepo,
        private readonly MermaConfigRepositoryInterface    $configRepo,
        private readonly MermaNotifierInterface            $notifier,
        private readonly LoggerInterface                   $logger,
    ) {}

    public function detect(ScaleReadingDTO $reading): ?ScaleEventResultDTO
    {
        // 1. Peso anterior (null = primera lectura de la balanza)
        $previousWeight = $this->readingRepo->findLastWeightBefore(
            $reading->scaleId,
            $reading->readAt
        );

        if ($previousWeight === null) {
            $this->logger->debug('MermaDetect: primera lectura scale={id}, sin evento', [
                'id' => $reading->scaleId,
            ]);
            return null;
        }

        // 2. Configuración de merma del producto (auto-crea si no existe)
        $config = $this->configRepo->findByProductId($reading->productId)
            ?? $this->configRepo->createDefaultForProduct($reading->productId);

        // 3. Clasificar el evento (lógica pura en el Service de dominio)
        $classification = $this->detector->classify(
            previousWeight:  $previousWeight,
            newWeight:       $reading->weightKg,
            readAt:          $reading->readAt,
            config:          $config,
        );

        // Sin evento (delta por debajo del umbral de ruido)
        if ($classification === null) {
            return null;
        }

        // 4. Construir y persistir la entidad
        $event = new ScaleEvent();
        $event->setScaleId($reading->scaleId)
            ->setProductId($reading->productId)
            ->setType($classification->type)
            ->setWeightBefore($previousWeight)
            ->setWeightAfter($reading->weightKg)
            ->setDeltaKg($classification->deltaKg)
            ->setDeltaCost($classification->deltaCost)
            ->setDetectedAt($reading->readAt);

        $this->eventRepo->save($event);

        $this->logger->info('ScaleEvent: {type} scale={s} delta={d}kg', [
            'type' => $classification->type,
            's'    => $reading->scaleId,
            'd'    => $classification->deltaKg,
        ]);

        // 5. Notificación si es anomalía y el cliente lo tiene activo
        if ($event->isAnomalia() && $config->isAlertOnAnomaly()) {
            $this->notifier->sendAnomalyAlert($event);
        }

        return new ScaleEventResultDTO(
            eventId:     $event->getId(),
            type:        $event->getType(),
            weightBefore: $previousWeight,
            weightAfter:  $reading->weightKg,
            deltaKg:      $classification->deltaKg,
            deltaCost:    $classification->deltaCost,
            detectedAt:   $reading->readAt,
        );
    }
}