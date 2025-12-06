<?php

namespace App\Report\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GenerateReportNowRequest
{
    #[Assert\NotBlank(message: 'UUID del cliente es requerido')]
    #[Assert\Uuid]
    private string $uuidClient;

    #[Assert\NotBlank(message: 'El nombre es requerido')]
    #[Assert\Length(max: 150)]
    private string $name;

    #[Assert\NotBlank(message: 'El tipo de informe es requerido')]
    #[Assert\Choice(choices: ['csv', 'pdf'], message: 'Tipo de informe inválido')]
    private string $reportType;

    #[Assert\NotBlank(message: 'El filtro de producto es requerido')]
    #[Assert\Choice(choices: ['all', 'below_stock', 'specific'], message: 'Filtro de producto inválido')]
    private string $productFilter;

    #[Assert\NotBlank(message: 'El email es requerido')]
    #[Assert\Email(message: 'Email inválido')]
    private string $email;

    #[Assert\NotBlank(message: 'El período es requerido')]
    #[Assert\Choice(choices: ['daily', 'weekly', 'monthly'], message: 'Período inválido')]
    private string $period = 'daily';

    private ?string $uuidUser = null;
    private ?\DateTimeImmutable $timestamp = null;

    /**
     * Array de IDs de productos (requerido solo si productFilter = 'specific')
     * La validación se hace en el UseCase
     *
     * @var array<int>
     */
    private array $productIds = [];

    /**
     * Constructor con named parameters (mantener compatibilidad con código existente)
     */
    public function __construct(
        string $uuidClient,
        string $name,
        string $reportType,
        string $productFilter,
        string $email,
        string $period = 'daily',
        array $productIds = []
    ) {
        $this->uuidClient = $uuidClient;
        $this->name = $name;
        $this->reportType = $reportType;
        $this->productFilter = $productFilter;
        $this->email = $email;
        $this->period = $period;
        $this->productIds = $productIds;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getReportType(): string
    {
        return $this->reportType;
    }

    public function getProductFilter(): string
    {
        return $this->productFilter;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPeriod(): string
    {
        return $this->period;
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