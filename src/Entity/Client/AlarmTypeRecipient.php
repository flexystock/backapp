<?php

namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\Alarm\Infrastructure\OutputAdapters\Repositories\AlarmTypeRecipientRepository')]
#[ORM\Table(name: 'alarm_type_recipients')]
#[ORM\UniqueConstraint(name: 'uniq_client_alarm_email', columns: ['uuid_client', 'alarm_type_id', 'email'])]
#[ORM\Index(name: 'idx_uuid_client', columns: ['uuid_client'])]
#[ORM\Index(name: 'idx_alarm_type_id', columns: ['alarm_type_id'])]
class AlarmTypeRecipient
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 36)]
    private string $uuid_client;

    #[ORM\ManyToOne(targetEntity: AlarmType::class)]
    #[ORM\JoinColumn(name: 'alarm_type_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private AlarmType $alarmType;

    #[ORM\Column(type: 'string', length: 255)]
    private string $email;

    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $uuid_user_creation = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $datehour_creation;

    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $uuid_user_modification = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $datehour_modification = null;

    public function __construct()
    {
        $this->datehour_creation = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuidClient(): string
    {
        return $this->uuid_client;
    }

    public function setUuidClient(string $uuidClient): self
    {
        $this->uuid_client = $uuidClient;

        return $this;
    }

    public function getAlarmType(): AlarmType
    {
        return $this->alarmType;
    }

    public function setAlarmType(AlarmType $alarmType): self
    {
        $this->alarmType = $alarmType;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUuidUserCreation(): ?string
    {
        return $this->uuid_user_creation;
    }

    public function setUuidUserCreation(?string $uuidUserCreation): self
    {
        $this->uuid_user_creation = $uuidUserCreation;

        return $this;
    }

    public function getDatehourCreation(): \DateTimeInterface
    {
        return $this->datehour_creation;
    }

    public function setDatehourCreation(\DateTimeInterface $datehourCreation): self
    {
        $this->datehour_creation = $datehourCreation;

        return $this;
    }

    public function getUuidUserModification(): ?string
    {
        return $this->uuid_user_modification;
    }

    public function setUuidUserModification(?string $uuidUserModification): self
    {
        $this->uuid_user_modification = $uuidUserModification;

        return $this;
    }

    public function getDatehourModification(): ?\DateTimeInterface
    {
        return $this->datehour_modification;
    }

    public function setDatehourModification(?\DateTimeInterface $datehourModification): self
    {
        $this->datehour_modification = $datehourModification;

        return $this;
    }
}
