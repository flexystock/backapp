<?php

namespace App\Alarm\Infrastructure\OutputAdapters\Repositories;

use App\Alarm\Application\OutputPorts\Repositories\AlarmTypeRecipientRepositoryInterface;
use App\Entity\Client\AlarmType;
use App\Entity\Client\AlarmTypeRecipient;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class AlarmTypeRecipientRepository extends EntityRepository implements AlarmTypeRecipientRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($entityManager, $entityManager->getClassMetadata(AlarmTypeRecipient::class));
    }

    public function findEmailsByClientAndType(string $uuidClient, int $alarmTypeId): array
    {
        $qb = $this->createQueryBuilder('atr')
            ->select('atr.email')
            ->where('atr.uuid_client = :uuidClient')
            ->andWhere('atr.alarmType = :alarmTypeId')
            ->setParameter('uuidClient', $uuidClient)
            ->setParameter('alarmTypeId', $alarmTypeId);

        $result = $qb->getQuery()->getResult();

        return array_map(fn($row) => $row['email'], $result);
    }

    public function addRecipient(
        string $uuidClient,
        int $alarmTypeId,
        string $email,
        ?string $uuidUserCreation = null
    ): AlarmTypeRecipient {
        $alarmType = $this->entityManager->find(AlarmType::class, $alarmTypeId);
        if (!$alarmType) {
            throw new \RuntimeException('ALARM_TYPE_NOT_FOUND');
        }

        $recipient = new AlarmTypeRecipient();
        $recipient->setUuidClient($uuidClient);
        $recipient->setAlarmType($alarmType);
        $recipient->setEmail($email);
        $recipient->setUuidUserCreation($uuidUserCreation);
        $recipient->setDatehourCreation(new \DateTime());

        $this->entityManager->persist($recipient);
        $this->entityManager->flush();

        return $recipient;
    }

    public function deleteRecipient(int $id, string $uuidClient): bool
    {
        $recipient = $this->createQueryBuilder('atr')
            ->where('atr.id = :id')
            ->andWhere('atr.uuid_client = :uuidClient')
            ->setParameter('id', $id)
            ->setParameter('uuidClient', $uuidClient)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$recipient) {
            return false;
        }

        $this->entityManager->remove($recipient);
        $this->entityManager->flush();

        return true;
    }

    public function findById(int $id): ?AlarmTypeRecipient
    {
        return $this->find($id);
    }

    public function findByClient(string $uuidClient): array
    {
        return $this->createQueryBuilder('atr')
            ->where('atr.uuid_client = :uuidClient')
            ->setParameter('uuidClient', $uuidClient)
            ->getQuery()
            ->getResult();
    }

    public function findByClientAndType(string $uuidClient, int $alarmTypeId): array
    {
        return $this->createQueryBuilder('atr')
            ->where('atr.uuid_client = :uuidClient')
            ->andWhere('atr.alarmType = :alarmTypeId')
            ->setParameter('uuidClient', $uuidClient)
            ->setParameter('alarmTypeId', $alarmTypeId)
            ->getQuery()
            ->getResult();
    }
}
