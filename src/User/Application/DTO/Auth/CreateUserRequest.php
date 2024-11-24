<?php
namespace App\User\Application\DTO\Auth;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUserRequest
{
    #[Assert\NotBlank(message: "El nombre completo es obligatorio.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "El nombre completo no puede tener más de {{ limit }} caracteres."
    )]
    #[Assert\Regex(
        pattern: '/^(?=.{1,50}$)[A-Za-zÁÉÍÓÚáéíóúÑñÜü]+(?:[-\'][A-Za-zÁÉÍÓÚáéíóúÑñÜü]+)?(?: [A-Za-zÁÉÍÓÚáéíóúÑñÜü]+(?:[-\'][A-Za-zÁÉÍÓÚáéíóúÑñÜü]+)?)*$/u',
        message: "El nombre completo no es válido."
    )]
    #[SerializedName('full_name')]
    private string $name;

    #[Assert\NotBlank(message: "Los apellidos son obligatorios.")]
    #[Assert\Regex(
        pattern: "/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü ]{1,255}$/",
        message: "Los apellidos solo pueden contener letras y espacios."
    )]
    #[SerializedName('surnames')]
    private string $surnames;

    #[Assert\NotBlank(message: "El correo electrónico es obligatorio.")]
    #[Assert\Email(message: "El correo electrónico '{{ value }}' no es válido.")]
    #[Assert\Regex(
        pattern: "/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,6}$/",
        message: "El correo electrónico debe seguir el formato correcto."
    )]
    private string $email;

    #[Assert\NotBlank(message: "La contraseña es obligatoria.")]
    #[Assert\Length(
        min: 8,
        minMessage: "La contraseña debe tener al menos {{ limit }} caracteres."
    )]
    #[Assert\Regex(
        pattern:"/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?\":{}|<>])[A-Za-z\d!@#$%^&*(),.?\":{}|<>]{6,}$/",
        message:"La contraseña debe tener al menos 8 caracteres, incluyendo una letra mayúscula, una minúscula, un número y un carácter especial."
    )]
    #[SerializedName('password')]
    private string $pass;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric', message: "El número de teléfono debe ser numérico.")]
    #[Assert\Length(
        min: 9,
        max: 15,
        minMessage: "El número de teléfono debe tener al menos {{ limit }} dígitos.",
        maxMessage: "El número de teléfono no puede tener más de {{ limit }} dígitos."
    )]
    #[Assert\Regex(
        pattern: "/^\d{9,15}$/",
        message: "El número de teléfono debe contener entre 9 y 15 dígitos numéricos."
    )]
    #[SerializedName('phone_number')]
    private string $phone;

    #[Assert\NotBlank]
    #[Assert\Choice(
        choices: ['DNI', 'NIE'],
        message: "El tipo de documento debe ser 'DNI' o 'NIE'."
    )]
    #[SerializedName('document_type')]
    private string $documentType;

    #[Assert\NotBlank]
    #[Assert\Length(
        max: 20,
        maxMessage: "El número de documento no puede tener más de {{ limit }} caracteres."
    )]
    #[Assert\Regex(
        pattern: "/^[A-Za-z0-9-]+$/",
        message: "El número de documento solo puede contener letras, números y guiones."
    )]
    #[SerializedName('document_number')]
    private string $documentNumber;

    #[Assert\NotBlank]
    #[Assert\Timezone(message: "La zona horaria '{{ value }}' no es válida.")]
    private string $timezone;

    #[Assert\NotBlank]
    private string $language;

    #[Assert\NotBlank]
    #[Assert\Choice(
        choices: ['email', 'phone'],
        message: "El método de contacto preferido debe ser 'email', 'phone' o 'sms'."
    )]
    #[SerializedName('preferred_contact_method')]
    private string $preferredContactMethod;

    #[Assert\NotNull]
    #[Assert\Type(type: 'bool', message: "El valor debe ser verdadero o falso.")]
    #[SerializedName('two_factor_enabled')]
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

    public function getSecurityQuestion(): string
    {
        return $this->securityQuestion;
    }

    public function setSecurityQuestion(string $securityQuestion): self
    {
        $this->securityQuestion = $securityQuestion;
        return $this;
    }

    public function getSecurityAnswer(): string
    {
        return $this->securityAnswer;
    }

    public function setSecurityAnswer(string $securityAnswer): self
    {
        $this->securityAnswer = $securityAnswer;
        return $this;
    }
}