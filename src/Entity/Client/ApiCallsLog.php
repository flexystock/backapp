<?php

namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'api_calls_log')]
class ApiCallsLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $request_at;

    #[ORM\Column(type: 'string', length: 255)]
    private string $endpoint;

    #[ORM\Column(type: 'string', length: 45)]
    private string $ip;

    #[ORM\Column(type: 'decimal', precision: 7, scale: 4)]
    private float $processing_time;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $http_code;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $request_data = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $response_data = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getRequestAt(): \DateTimeInterface
    {
        return $this->request_at;
    }

    public function setRequestAt(\DateTimeInterface $request_at): void
    {
        $this->request_at = $request_at;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    public function getProcessingTime(): float
    {
        return $this->processing_time;
    }

    public function setProcessingTime(float $processing_time): void
    {
        $this->processing_time = $processing_time;
    }

    public function getHttpCode(): int
    {
        return $this->http_code;
    }

    public function setHttpCode(int $http_code): void
    {
        $this->http_code = $http_code;
    }

    public function getRequestData(): ?string
    {
        return $this->request_data;
    }

    public function setRequestData(?string $request_data): void
    {
        $this->request_data = $request_data;
    }

    public function getResponseData(): ?string
    {
        return $this->response_data;
    }

    public function setResponseData(?string $response_data): void
    {
        $this->response_data = $response_data;
    }
}
