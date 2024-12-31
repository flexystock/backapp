<?php

namespace App\Entity\Main;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'pool_ttn_device')]
class PoolTtnDevice
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;
    #[ORM\Column(type: 'boolean')]
    private bool $available;
    #[ORM\Column(type: 'string', length: 100)]
    private string $end_device_id;
    #[ORM\Column(type: 'string', length: 36)]
    private string $end_device_name;
    #[ORM\Column(type: 'string', length: 100)]
    private string $appEUI;
    #[ORM\Column(type: 'string', length: 100)]
    private string $devEUI;
    #[ORM\Column(type: 'string', length: 100)]
    private string $appKey;
    #[ORM\Column(type: 'string', length: 36)]
    private string $uuid_user_creation;
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $datehour_creation;
    #[ORM\Column(type: 'string', length: 36)]
    private ?string $uuid_user_modification;
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $datehour_modification;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): self
    {
        $this->available = $available;

        return $this;
    }

    public function getEndDeviceId(): ?string
    {
        return $this->end_device_id;
    }

    public function setEndDeviceId(string $endDeviceId): self
    {
        $this->end_device_id = $endDeviceId;

        return $this;
    }
    public function getEndDeviceName(): ?string
    {
        return $this->end_device_name;
    }

    public function setEndDeviceName(string $endDeviceName): self
    {
        $this->end_device_name = $endDeviceName;

        return $this;
    }

    public function getAppEUI(): ?string
    {
        return $this->appEUI;
    }

    public function setAppEUI(string $appEUI): self
    {
        $this->appEUI = $appEUI;

        return $this;
    }

    public function getDevEUI(): ?string
    {
        return $this->devEUI;
    }

    public function setDevEUI(string $devEUI): self
    {
        $this->devEUI = $devEUI;

        return $this;
    }

    public function getAppKey(): ?string
    {
        return $this->appKey;
    }

    public function setAppKey(string $appKey): self
    {
        $this->appKey = $appKey;

        return $this;
    }

    public function getUuidUserCreation(): ?string
    {
        return $this->uuid_user_creation;
    }

    public function setUuidUserCreation(string $uuidUserCreation): self
    {
        $this->uuid_user_creation = $uuidUserCreation;

        return $this;
    }

    public function getDatehourCreation(): ?\DateTimeInterface
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

    public function setUuidUserModification(string $uuidUserModification): self
    {
        $this->uuid_user_modification = $uuidUserModification;

        return $this;
    }

    public function getDatehourModification(): ?\DateTimeInterface
    {
        return $this->datehour_modification;
    }

    public function setDatehourModification(\DateTimeInterface $datehourModification): self
    {
        $this->datehour_modification = $datehourModification;

        return $this;
    }


}