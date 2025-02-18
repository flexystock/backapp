<?php
// src/Entity/Client/AlarmConfig.php
namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'alarm_config')]
class AlarmConfig
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $alarm_name;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $product_id; // Si usas UUID para producto, cambia el tipo a string

    // Relación con AlarmType
    #[ORM\ManyToOne(targetEntity: AlarmType::class)]
    #[ORM\JoinColumn(name: 'alarm_type_id', referencedColumnName: 'id', nullable: false)]
    private AlarmType $alarm_type;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?float $percentage_threshold = null;

    #[ORM\Column(type: 'string', length: 36)]
    private string $uuid_user_creation;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $datehour_creation;

    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $uuid_user_modification = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $datehour_modification = null;

    // Getters y Setters

    public function getId(): int
    {
        return $this->id;
    }

    public function getAlarmName(): string
    {
        return $this->alarm_name;
    }

    public function setAlarmName(string $alarmName): self
    {
        $this->alarm_name = $alarmName;
        return $this;
    }

    public function getProductId(): int
    {
        return $this->product_id;
    }

    public function setProductId(int $productId): self
    {
        $this->product_id = $productId;
        return $this;
    }

    public function getAlarmType(): AlarmType
    {
        return $this->alarm_type;
    }

    public function setAlarmType(AlarmType $alarmType): self
    {
        $this->alarm_type = $alarmType;
        return $this;
    }

    public function getPercentageThreshold(): ?float
    {
        return $this->percentage_threshold;
    }

    public function setPercentageThreshold(?float $percentageThreshold): self
    {
        $this->percentage_threshold = $percentageThreshold;
        return $this;
    }

    public function getUuidUserCreation(): string
    {
        return $this->uuid_user_creation;
    }

    public function setUuidUserCreation(string $uuidUserCreation): self
    {
        $this->uuid_user_creation = $uuidUserCreation;
        return $this;
    }

    public function getCreationDate(): \DateTimeInterface
    {
        return $this->datehour_creation;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->datehour_creation = $creationDate;
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

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->datehour_modification;
    }

    public function setUpdateDate(?\DateTimeInterface $updateDate): self
    {
        $this->datehour_modification = $updateDate;
        return $this;
    }
}
