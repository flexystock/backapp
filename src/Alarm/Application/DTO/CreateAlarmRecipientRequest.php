<?php

namespace App\Alarm\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateAlarmRecipientRequest
{
    #[Assert\NotBlank(message: 'El uuidClient es obligatorio.')]
    #[Assert\Uuid(message: 'El uuidClient debe ser un UUID válido.')]
    private string $uuidClient;

    #[Assert\NotBlank(message: 'El alarmTypeId es obligatorio.')]
    #[Assert\Positive(message: 'El alarmTypeId debe ser un número positivo.')]
    private int $alarmTypeId;

    #[Assert\NotBlank(message: 'El email es obligatorio.')]
    #[Assert\Email(message: 'El email no tiene un formato válido.')]
    private string $email;

    private ?string $uuidUser = null;

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function setUuidClient(string $uuidClient): self
    {
        $this->uuidClient = $uuidClient;

        return $this;
    }

    public function getAlarmTypeId(): int
    {
        return $this->alarmTypeId;
    }

    public function setAlarmTypeId(int $alarmTypeId): self
    {
        $this->alarmTypeId = $alarmTypeId;

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
}
