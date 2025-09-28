<?php

// src/Event/MailSentEvent.php

namespace App\Event;

use App\Entity\Main\User;
use Symfony\Contracts\EventDispatcher\Event;

class MailSentEvent extends Event
{
    private string $recipient;
    private string $subject;
    private ?string $body;
    private string $status;
    private ?string $errorMessage;
    private \DateTimeInterface $sentAt;
    private ?array $additionalData;
    private ?User $user;
    private ?int $errorCode;
    private ?string $errorType;
    private string $logTarget;

    public function __construct(
        string $recipient,
        string $subject,
        ?string $body,
        string $status,
        ?string $errorMessage,
        ?int $errorCode,
        \DateTimeInterface $sentAt,
        ?array $additionalData = null,
        ?string $errorType = null,
        ?User $user = null,
        string $logTarget = MailLogTarget::MAIN, // valor por defecto
    ) {
        $this->recipient = $recipient;
        $this->subject = $subject;
        $this->body = $body;
        $this->status = $status;
        $this->errorMessage = $errorMessage;
        $this->errorCode = $errorCode;
        $this->sentAt = $sentAt;
        $this->additionalData = $additionalData;
        $this->user = $user;
        $this->errorType = $errorType;
        $this->logTarget = $logTarget;
    }

    // Getters...
    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getSentAt(): \DateTimeInterface
    {
        return $this->sentAt;
    }

    public function getAdditionalData(): ?array
    {
        return $this->additionalData;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    public function getErrorType(): ?string
    {
        return $this->errorType;
    }

    public function getLogTarget(): string
    {
        return $this->logTarget;
    }
}
