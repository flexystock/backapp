<?php

namespace App\Ttn\Application\Services;

use App\Entity\Client\Scales;
use App\Entity\Client\WeightsLog;
use Doctrine\ORM\EntityManagerInterface;

class WeightsLogService
{
    public function createLog(
        EntityManagerInterface $entityManager,
        Scales $scale,
        float $realWeightKg,
        int $weightGrams,
        float $voltage,
        float $chargePercentage
    ): WeightsLog {
        $weightLog = new WeightsLog();
        $weightLog->setScale($scale);
        $weightLog->setProduct($scale->getProduct());
        $weightLog->setDate(new \DateTime());
        $weightLog->setRealWeight($realWeightKg);
        $weightLog->setWeightGrams($weightGrams);
        $weightLog->setAdjustWeight($realWeightKg);
        $weightLog->setVoltage($voltage);
        $weightLog->setChargePercentage($chargePercentage);

        $entityManager->persist($weightLog);
        $entityManager->flush();

        return $weightLog;
    }

    public function updateMinorVariationLog(
        WeightsLog $existingLog,
        int $weightGrams,
        float $voltage,
        float $chargePercentage,
        EntityManagerInterface $entityManager
    ): void {
        $existingLog->setWeightGrams($weightGrams);
        //$existingLog->setDate(new \DateTime());
        $existingLog->setVoltage($voltage);
        $existingLog->setChargePercentage($chargePercentage);

        $entityManager->flush();
    }
}
