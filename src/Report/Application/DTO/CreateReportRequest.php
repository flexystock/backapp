<?php

namespace App\Report\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateReportRequest
{
    #[Assert\NotBlank(message: 'UUID del cliente es requerido')]
    #[Assert\Uuid]
    private string $uuidClient;

    #[Assert\NotBlank(message: 'El nombre es requerido')]
    #[Assert\Length(max: 150)]
    private string $name;

    #[Assert\NotBlank(message: 'El período es requerido')]
    #[Assert\Choice(choices: ['daily', 'weekly', 'monthly'], message: 'Período inválido')]
    private string $period;

    #[Assert\NotBlank(message: 'La hora de envío es requerida')]
    private string $sendTime;

    #[Assert\NotBlank(message: 'El tipo de informe es requerido')]
    #[Assert\Choice(choices: ['csv', 'pdf'], message: 'Tipo de informe inválido')]
    private string $reportType;

    #[Assert\Choice(choices: ['all', 'below_stock', 'specific'], message: 'Filtro de producto inválido')]
    private string $productFilter = 'all';

    #[Assert\NotBlank(message: 'El email es requerido')]
    #[Assert\Email(message: 'Email inválido')]
    private string $email;

    private ?string $uuidUser = null;
    private ?\DateTimeImmutable $timestamp = null;

    /**
     * Array de IDs de productos (requerido solo si productFilter = 'specific')
     * La validación se hace en el UseCase/Controller
     *
     * @var array<int>
     */
    private array $productIds = [];

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function setUuidClient(string $uuidClient): self
    {
        $this->uuidClient = $uuidClient;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPeriod(): string
    {
        return $this->period;
    }

    public function setPeriod(string $period): self
    {
        $this->period = $period;
        return $this;
    }

    public function getSendTime(): string
    {
        return $this->sendTime;
    }

    public function setSendTime(string $sendTime): self
    {
        $this->sendTime = $sendTime;
        return $this;
    }

    public function getReportType(): string
    {
        return $this->reportType;
    }

    public function setReportType(string $reportType): self
    {
        $this->reportType = $reportType;
        return $this;
    }

    public function getProductFilter(): string
    {
        return $this->productFilter;
    }

    public function setProductFilter(string $productFilter): self
    {
        $this->productFilter = $productFilter;
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

    public function getUuidUser(): ?string
    {
        return $this->uuidUser;
    }

    public function setUuidUser(?string $uuidUser): self
    {
        $this->uuidUser = $uuidUser;
        return $this;
    }

    public function getTimestamp(): ?\DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(?\DateTimeImmutable $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return array<int>
     */
    public function getProductIds(): array
    {
        return $this->productIds;
    }

    /**
     * @param array<int> $productIds
     */
    public function setProductIds(array $productIds): self
    {
        $this->productIds = $productIds;
        return $this;
    }
}