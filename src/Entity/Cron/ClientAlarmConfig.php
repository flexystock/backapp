<?php
// src/Entity/Cron/ClientAlarmConfig.php

namespace App\Entity\Cron;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'client_alarm_config')]
class ClientAlarmConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 36)]
    private string $client_id;

    #[ORM\Column(type: 'integer')]
    private int $product_id;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private string $alarm_threshold; // Usamos string para valores DECIMAL

    #[ORM\Column(type: 'string', length: 50)]
    private string $alarm_type;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $created_at;

    // Getters y Setters

    public function getId(): int
    {
        return $this->id;
    }

    public function getClientId(): string
    {
        return $this->client_id;
    }

    public function setClientId(string $clientId): self
    {
        $this->client_id = $clientId;
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

    public function getAlarmThreshold(): string
    {
        return $this->alarm_threshold;
    }

    public function setAlarmThreshold(string $alarmThreshold): self
    {
        $this->alarm_threshold = $alarmThreshold;
        return $this;
    }

    public function getAlarmType(): string
    {
        return $this->alarm_type;
    }

    public function setAlarmType(string $alarmType): self
    {
        $this->alarm_type = $alarmType;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->created_at = $createdAt;
        return $this;
    }
}
