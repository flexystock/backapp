<?php

namespace App\Ttn\Application\DTO;

class RegisterTtnAppRequest
{
    private string $applicationId;
    private string $name;
    private string $description;
    private string $uuidUserCreation;
    private \DateTimeInterface $datehourCreation;
    private string $uuidClient;

    public function __construct(string $applicationId, string $name, string $description,
        string $uuidUserCreation, \DateTimeInterface $datehourCreation, string $uuidClient)
    {
        $this->applicationId = $applicationId;
        $this->name = $name;
        $this->description = $description;
        $this->uuidUserCreation = $uuidUserCreation;
        $this->datehourCreation = $datehourCreation;
        $this->uuidClient = $uuidClient;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getUuidUserCreation(): string
    {
        return $this->uuidUserCreation;
    }

    public function getDatehourCreation(): \DateTimeInterface
    {
        return $this->datehourCreation;
    }

    public function getApplicationId(): string
    {
        return $this->applicationId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setApplicationId(string $applicationId): self
    {
        $this->applicationId = $applicationId;

        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
