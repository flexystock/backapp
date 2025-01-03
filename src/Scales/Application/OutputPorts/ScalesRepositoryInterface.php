<?php

namespace App\Scales\Application\OutputPorts;

use App\Entity\Client\Scales;

interface ScalesRepositoryInterface
{
    /**
     * Si usas multi-bbdd, aquí podrías inyectar la lógica
     * para cambiar la conexión según uuidClient.
     */
    public function selectClientConnection(string $uuidClient): void;

    public function save(Scales $scales): void;

    // Otras operaciones (findOneBy..., etc.)
}
