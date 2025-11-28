<?php

namespace App\Report\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateReportRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_CLIENT_ID')]
    #[Assert\Uuid(message: 'INVALID_CLIENT_ID')]
    private string $uuidClient;

    #[Assert\NotBlank(message: 'REQUIRED_NAME')]
    #[Assert\Length(max: 150, maxMessage: 'NAME_TOO_LONG')]
    private string $name;

    #[Assert\NotBlank(message: 'REQUIRED_PERIOD')]
    #[Assert\Length(max: 50, maxMessage: 'PERIOD_TOO_LONG')]
    private string $period;

    #[Assert\NotBlank(message: 'REQUIRED_SEND_TIME')]
    private string $sendTime;

    #[Assert\NotBlank(message: 'REQUIRED_REPORT_TYPE')]
    #[Assert\Length(max: 50, maxMessage: 'REPORT_TYPE_TOO_LONG')]
    private string $reportType;

    #[Assert\Length(max: 255, maxMessage: 'PRODUCT_FILTER_TOO_LONG')]
    private ?string $productFilter = null;

    #[Assert\NotBlank(message: 'REQUIRED_EMAIL')]
    #[Assert\Email(message: 'INVALID_EMAIL')]
    #[Assert\Length(max: 255, maxMessage: 'EMAIL_TOO_LONG')]
    private string $email;

    private ?string $uuidUser = null;

    private ?\DateTimeInterface $timestamp = null;

    public function __construct(
        string $uuidClient,
        string $name,
        string $period,
        string $sendTime,
        string $reportType,
        string $email,
        ?string $productFilter = null,
    ) {
        $this->uuidClient = $uuidClient;
        $this->name = $name;
        $this->period = $period;
        $this->sendTime = $sendTime;
        $this->reportType = $reportType;
        $this->email = $email;
        $this->productFilter = $productFilter;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPeriod(): string
    {
        return $this->period;
    }

    public function getSendTime(): string
    {
        return $this->sendTime;
    }

    public function getReportType(): string
    {
        return $this->reportType;
    }

    public function getProductFilter(): ?string
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
