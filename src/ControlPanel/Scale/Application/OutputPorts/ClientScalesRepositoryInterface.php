<?php

declare(strict_types=1);

namespace App\ControlPanel\Scale\Application\OutputPorts;

interface ClientScalesRepositoryInterface
{
    /**
     * Get voltage percentage for scales from client databases.
     * Returns an associative array with end_device_id as key and voltage_percentage as value.
     *
     * @param array $scalesByClient array where keys are client UUIDs and values are arrays of end_device_ids
     *
     * @return array associative array [end_device_id => voltage_percentage]
     */
    public function getVoltagePercentagesByClient(array $scalesByClient): array;

    /**
     * Get last send timestamps for scales from client databases.
     * Returns an associative array with end_device_id as key and DateTimeImmutable|null as value.
     *
     * @param array $scalesByClient array where keys are client UUIDs and values are arrays of end_device_ids
     *
     * @return array associative array [end_device_id => DateTimeImmutable|null]
     */
    public function getLastSendTimestampsByClient(array $scalesByClient): array;
}
