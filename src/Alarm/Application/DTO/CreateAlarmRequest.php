<?php

namespace App\Alarm\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateAlarmRequest
{
    private string $uuidClient;

    private string $name;

    #[Assert\Choice(
        choices: [
            'stock',
            'horario',
        ],
        message: 'INVALID_TYPE'
    )]
    private string $type;

    #[Assert\Type(type: 'numeric', message: 'INVALID_THRESHOLD')]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'El umbral debe ser mayor o igual a 0.')]
    private float $percentageThreshold;

    private string $uuidUserCreation;

    private \DateTimeInterface $datehourCreation;

    public function __construct(
        string $uuidClient,
        string $name,
        string $type,
        float $percentageThreshold,
    ) {
        $this->uuidClient = $uuidClient;
        $this->name = $name;
        $this->type = $type;
        $this->percentageThreshold = $percentageThreshold;

    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPercentageThreshold(): float
    {
        return $this->percentageThreshold;
    }

    public function getUuidUserCreation(): string
    {
        return $this->uuidUserCreation;
    }

    public function getDatehourCreation(): \DateTimeInterface
    {
        return $this->datehourCreation;
    }

    public function setUuidClient(string $uuidClient): void
    {
        $this->uuidClient = $uuidClient;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setPercentageThreshold(float $percentageThreshold): void
    {
        $this->percentageThreshold = $percentageThreshold;
    }

    public function setUuidUserCreation(string $uuidUserCreation): void
    {
        $this->uuidUserCreation = $uuidUserCreation;
    }

    public function setDatehourCreation(\DateTimeInterface $datehourCreation): void
    {
        $this->datehourCreation = $datehourCreation;
    }
}
