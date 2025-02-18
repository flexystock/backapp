<?php
// src/Alarm/Infrastructure/OutputAdapters/Repositories/AlarmTypeRepository.php
namespace App\Alarm\Infrastructure\OutputAdapters\Repositories;

use App\Alarm\Application\OutputPorts\Repositories\AlarmTypeRepositoryInterface;
use App\Entity\Client\AlarmType;
use Doctrine\ORM\EntityManagerInterface;

class AlarmTypeRepository implements AlarmTypeRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Busca una entidad AlarmType por su campo type_name.
     */
    public function findByType(string $type): ?AlarmType
    {
        return $this->em->getRepository(AlarmType::class)->findOneBy([
            'type_name' => $type,
        ]);
    }
}
