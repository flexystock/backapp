<?php

namespace App\User\Application\DTO\Auth;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUserRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_FULL_NAME')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'INVALID_FULL_NAME'
    )]
    #[Assert\Regex(
        pattern: '/^(?=.{1,50}$)[A-Za-zÁÉÍÓÚáéíóúÑñÜü]+(?:[-\'][A-Za-zÁÉÍÓÚáéíóúÑñÜü]+)?(?: [A-Za-zÁÉÍÓÚáéíóúÑñÜü]+(?:[-\'][A-Za-zÁÉÍÓÚáéíóúÑñÜü]+)?)*$/u',
        message: 'INVALID_FULL_NAME'
    )]
    #[SerializedName('fullName')]
    private string $name;

    #[Assert\NotBlank(message: 'REQUIRED_SURNAMES')]
    #[Assert\Regex(
        pattern: '/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü ]{1,255}$/',
        message: 'INVALID_SURNAMES'
    )]
    #[SerializedName('surnames')]
    private string $surnames;

    #[Assert\NotBlank(message: 'REQUIRED_EMAIL')]
    #[Assert\Email(message: "INVALID_EMAIL")]
    #[Assert\Regex(
        pattern: "/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,6}$/",
        message: 'El correo electrónico debe seguir el formato correcto.'
    )]
    private string $email;

    #[Assert\NotBlank(message: 'REQUIRED_PASSWORD')]
    #[Assert\Length(
        min: 8,
        minMessage: 'INVALID_PASSWORD'
    )]
    #[Assert\Regex(
        pattern: "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?\":{}|<>])[A-Za-z\d!@#$%^&*(),.?\":{}|<>]{6,}$/",
        message: 'INVALID_PASSWORD'
    )]
    #[SerializedName('password')]
    private string $pass;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'string', message: 'INVALID_PHONE_NUMBER')]
    #[Assert\Length(
        min: 9,
        max: 15,
        minMessage: 'INVALID_PHONE_NUMBER',
        maxMessage: 'INVALID_PHONE_NUMBER'
    )]
    #[Assert\Regex(
        pattern: "/^\d{9,15}$/",
        message: 'INVALID_PHONE_NUMBER'
    )]
    #[SerializedName('phoneNumber')]
    private string $phone;

    #[Assert\NotBlank]
    #[Assert\Choice(
        choices: ['DNI', 'NIE'],
        message: "INVALID_DOCUMENT_TYPE"
    )]
    #[SerializedName('documentType')]
    private string $documentType;

    #[Assert\NotBlank]
    #[Assert\Length(
        max: 20,
        maxMessage: 'INVALID_DOCUMENT_NUMBER'
    )]
    #[Assert\Regex(
        pattern: '/^[A-Za-z0-9-]+$/',
        message: 'INVALID_DOCUMENT_NUMBER'
    )]
    #[SerializedName('documentNumber')]
    private string $documentNumber;

    #[Assert\NotBlank]
    #[Assert\Timezone(message: "INVALID_TIMEZONE")]
    private string $timezone;

    #[Assert\NotBlank]
    private string $language;

    #[Assert\NotBlank]
    #[Assert\Choice(
        choices: ['email', 'phone'],
        message: "INVALID_PREFERRED_CONTACT_METHOD"
    )]
    #[SerializedName('preferredContactMethod')]
    private string $preferredContactMethod;

    #[Assert\NotNull]
    #[Assert\Type(type: 'bool', message: 'El valor debe ser verdadero o falso.')]
    #[SerializedName('twoFactorEnabled')]

    private bool $twoFactorEnabled;
    // Métodos getters y setters

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurnames(): string
    {
        return $this->surnames;
    }

    public function setSurnames(string $surnames): self
    {
        $this->surnames = $surnames;

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

    public function getPass(): string
    {
        return $this->pass;
    }

    public function setPass(string $pass): self
    {
        $this->pass = $pass;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getDocumentType(): string
    {
        return $this->documentType;
    }

    public function setDocumentType(string $documentType): self
    {
        $this->documentType = $documentType;

        return $this;
    }

    public function getDocumentNumber(): string
    {
        return $this->documentNumber;
    }

    public function setDocumentNumber(string $documentNumber): self
    {
        $this->documentNumber = $documentNumber;

        return $this;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getPreferredContactMethod(): string
    {
        return $this->preferredContactMethod;
    }

    public function setPreferredContactMethod(string $preferredContactMethod): self
    {
        $this->preferredContactMethod = $preferredContactMethod;

        return $this;
    }

    public function isTwoFactorEnabled(): bool
    {
        return $this->twoFactorEnabled;
    }

    public function setTwoFactorEnabled(bool $twoFactorEnabled): self
    {
        $this->twoFactorEnabled = $twoFactorEnabled;

        return $this;
    }

}
