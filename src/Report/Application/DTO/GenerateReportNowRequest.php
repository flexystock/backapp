<?php

namespace App\Report\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GenerateReportNowRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_CLIENT_ID')]
    #[Assert\Uuid(message: 'INVALID_CLIENT_ID')]
    private string $uuidClient;

    #[Assert\NotBlank(message: 'REQUIRED_NAME')]
    #[Assert\Length(max: 150, maxMessage: 'NAME_TOO_LONG')]
    private string $name;

    #[Assert\NotBlank(message: 'REQUIRED_REPORT_TYPE')]
    #[Assert\Choice(choices: ['csv', 'pdf'], message: 'INVALID_REPORT_TYPE')]
    private string $reportType;

    #[Assert\NotBlank(message: 'REQUIRED_PRODUCT_FILTER')]
    #[Assert\Choice(choices: ['all', 'below_stock'], message: 'INVALID_PRODUCT_FILTER')]
    private string $productFilter;

    #[Assert\NotBlank(message: 'REQUIRED_EMAIL')]
    #[Assert\Email(message: 'INVALID_EMAIL')]
    #[Assert\Length(max: 255, maxMessage: 'EMAIL_TOO_LONG')]
    private string $email;

    private ?string $uuidUser = null;

    private ?\DateTimeInterface $timestamp = null;

    public function __construct(
        string $uuidClient,
        string $name,
        string $reportType,
        string $productFilter,
        string $email,
    ) {
        $this->uuidClient = $uuidClient;
        $this->name = $name;
        $this->reportType = $reportType;
        $this->productFilter = $productFilter;
        $this->email = $email;
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

    public function getUuidUser(): ?string
    {
        return $this->uuidUser;
    }

    public function setUuidUser(?string $uuidUser): void
    {
        $this->uuidUser = $uuidUser;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(?\DateTimeInterface $timestamp): void
    {
        $this->timestamp = $timestamp;
    }
}
