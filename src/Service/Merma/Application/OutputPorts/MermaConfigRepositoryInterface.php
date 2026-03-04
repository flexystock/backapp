<?php

namespace App\Service\Merma\Application\OutputPorts;

use App\Entity\Client\MermaConfig;
use App\Entity\Client\MermaMonthlyReport;
use App\Entity\Client\ScaleEvent;

/**
 * Lo que Merma necesita de la configuración de merma por producto.
 */
interface MermaConfigRepositoryInterface
{
    public function save(MermaConfig $config): void;

    public function findByProductId(int $productId): ?MermaConfig;

    /**
     * Crea y persiste una config con valores por defecto.
     * Llamado automáticamente si el producto no tiene config aún.
     */
    public function createDefaultForProduct(int $productId): MermaConfig;
}