<?php
// src/Alarm/Application/OutputPorts/Repositories/AlarmTypeRepositoryInterface.php
namespace App\Alarm\Application\OutputPorts\Repositories;

use App\Entity\Client\AlarmType;

interface AlarmTypeRepositoryInterface
{
    /**
     * Busca y devuelve una entidad AlarmType según el tipo.
     *
     * @param string $type El tipo de alarma (por ejemplo, "stock", "horario", etc.)
     *
     * @return AlarmType|null
     */
    public function findByType(string $type): ?AlarmType;
}
