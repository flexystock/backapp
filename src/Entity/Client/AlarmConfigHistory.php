<?php
// src/Entity/Client/AlarmConfigHistory.php
namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'alarm_config_history')]
class AlarmConfigHistory
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    // Relación con AlarmConfig
    #[ORM\ManyToOne(targetEntity: AlarmConfig::class)]
    #[ORM\JoinColumn(name: 'alarm_config_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private AlarmConfig $alarm_config_id;

    #[ORM\Column(type: 'string', length: 36)]
    private string $uuid_user_modification;

    #[ORM\Column(type: 'text')]
    private string $data_alarm_before_modification;

    #[ORM\Column(type: 'text')]
    private string $data_alarm_after_modification;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $datehour_modification;

    // Getters y Setters

    public function getId(): int
    {
        return $this->id;
    }

    public function getAlarmConfig(): AlarmConfig
    {
        return $this->alarm_config_id;
    }

    public function setAlarmConfig(AlarmConfig $alarmConfig): self
    {
        $this->alarm_config_id = $alarmConfig;
        return $this;
    }

    public function getUuidUserModification(): string
    {
        return $this->uuid_user_modification;
    }

    public function setUuidUserModification(string $uuidUserModification): self
    {
        $this->uuid_user_modification = $uuidUserModification;
        return $this;
    }

    public function getDataAlarmBeforeModification(): string
    {
        return $this->data_alarm_before_modification;
    }

    public function setDataAlarmBeforeModification(string $dataAlarmBeforeModification): self
    {
        $this->data_alarm_before_modification = $dataAlarmBeforeModification;
        return $this;
    }

    public function getDataAlarmAfterModification(): string
    {
        return $this->data_alarm_after_modification;
    }

    public function setDataAlarmAfterModification(string $dataAlarmAfterModification): self
    {
        $this->data_alarm_after_modification = $dataAlarmAfterModification;
        return $this;
    }

    public function getDateModification(): \DateTimeInterface
    {
        return $this->datehour_modification;
    }

    public function setDateModification(\DateTimeInterface $dateModification): self
    {
        $this->datehour_modification = $dateModification;
        return $this;
    }
}
