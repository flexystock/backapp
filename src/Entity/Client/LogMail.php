<?php

namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'log_mail')]
class LogMail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $recipient;

    #[ORM\Column(type: 'string', length: 255)]
    private string $subject;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $body = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $status;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $error_message = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $sent_at;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $additional_data = null;

    #[ORM\Column(type: 'integer')]
    private ?int $error_code = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $error_type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function setRecipient(string $recipient): void
    {
        $this->recipient = $recipient;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): void
    {
        $this->body = $body;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getErrorMessage(): ?string
    {
        return $this->error_message;
    }

    public function setErrorMessage(?string $errorMessage): void
    {
        $this->error_message = $errorMessage;
    }

    public function getSentAt(): \DateTimeInterface
    {
        return $this->sent_at;
    }

    public function setSentAt(\DateTimeInterface $sentAt): void
    {
        $this->sent_at = $sentAt;
    }

    public function getAdditionalData(): ?array
    {
        return $this->additional_data;
    }

    public function setAdditionalData(?array $additionalData): void
    {
        $this->additional_data = $additionalData;
    }

    public function getErrorCode(): ?int
    {
        return $this->error_code;
    }

    public function setErrorCode(?int $errorCode): void
    {
        $this->error_code = $errorCode;
    }

    public function getErrorType(): ?string
    {
        return $this->error_type;
    }

    public function setErrorType(?string $errorType): self
    {
        $this->error_type = $errorType;

        return $this;
    }
}