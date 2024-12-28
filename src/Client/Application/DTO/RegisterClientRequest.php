<?php

namespace App\Client\Application\DTO;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterClientRequest
{
    #[Assert\NotBlank(message: 'El id del usuario es obligatorio.')]
    #[Assert\Length(
        max: 36,
    )]
    #[SerializedName('userId')]
    private string $uuidUser;

    #[Assert\NotBlank(message: 'El nombre del cliente es obligatorio.')]
    #[Assert\Length(
        max: 255,
    )]
    #[SerializedName('name')]
    private string $name;

    #[Assert\Length(
        max: 255,
    )]
    #[SerializedName('companyName')]
    private string $businessGroupName;

    #[Assert\NotBlank(message: 'El NIF es obligatorio.')]
    #[Assert\Length(
        max: 12,
    )]
    private string $nifCif;

    #[Assert\NotBlank(message: 'La fecha de creación es obligatoria.')]
    private string $foundationDate;

    #[Assert\NotBlank(message: 'La direccion fiscal es obligatorio.')]
    #[Assert\Length(
        max: 255,
    )]
    private string $fiscalAddress;

    #[Assert\NotBlank(message: 'La direccion fisica es obligatorio.')]
    #[Assert\Length(
        max: 255,
    )]
    #[Assert\Regex(
        pattern: '/^[A-Za-z0-9áéíóúÁÉÍÓÚñÑüÜ\.,\/\-\s]{1,100}$/u',
        message: 'La dirección fiscal no es válida.'
    )]
    private string $physicalAddress;

    #[Assert\NotBlank(message: 'La ciudad es obligatorio.')]
    #[Assert\Length(
        max: 255,
    )]
    #[Assert\Regex(
        pattern: '/^[A-Za-zÁÉÍÓÚáéíóúÑñÜüçÇàáâ...]{1,50}(?:[ -][A-Za-zÁÉÍÓÚáéíóúÑñÜüçÇàáâ...]{1,50})*$/u',
        message: 'La ciudad no es válida.'
    )]
    private string $city;

    #[Assert\NotBlank(message: 'El pais es obligatorio.')]
    #[Assert\Length(
        max: 255,
    )]
    private string $country;

    #[Assert\NotBlank(message: 'El codigo postal es obligatorio.')]
    #[Assert\Regex(
        pattern: '/^[A-Za-z0-9À-ÿ\s\-]{3,10}$/u',
        message: 'El código postal no es válido.'
    )]
    private int $postalCode;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'string', message: 'El número de teléfono debe ser numérico.')]
    private string $companyPhone;

    #[Assert\NotBlank(message: 'El email de la compañia es obligatorio.')]
    #[Assert\Email(message: "El correo electrónico '{{ value }}' no es válido.")]
    private string $companyEmail;

    #[Assert\NotBlank(message: 'El numero de empleados es obligatorio.')]
    #[Assert\Positive(message: 'El número de empleados debe ser un número positivo.')]
    #[Assert\LessThanOrEqual(
        value: 999999,
        message: 'El número de empleados no puede exceder de 999999.'
    )]
    private int $numberOfEmployees;

    #[Assert\NotBlank(message: 'El sector industrial es obligatorio.')]
    #[Assert\Regex(
        pattern: '/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü0-9\s\.,\-\&]{1,50}$/u',
        message: 'El sector industrial no es válido.'
    )]
    private string $industrySector;

    #[Assert\NotBlank(message: 'El volumen de inevntario es obligatorio.')]
    #[Assert\Regex(
        pattern: '/^\d{1,10}$/',
        message: 'El volumen promedio de inventario no es válido.'
    )]
    private int $averageInventoryVolume;

    #[Assert\NotBlank(message: 'El tipo de moneda es obligatorio.')]
    #[Assert\Regex(
        pattern: '/^([A-Za-z]{3,15}(?:\s?[A-Za-z]{2,15})?|[€$¥£₣₹₣])$/u',
        message: 'La moneda no es válida.'
    )]
    private string $currency;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'int', message: 'El número de almacenes debe ser numérico.')]
    private int $numberWarehouses;

    #[Assert\NotBlank(message: 'El volumen anual de ventas es obligatorio.')]
    #[Assert\Regex(
        pattern: '/^\d{1,3}(?:\.\d{3})*(?:,([0-9]{1,2}))?$/',
        message: 'El volumen anual de ventas no es válido.'
    )]
    private int $annualSalesVolume;

    public function getUuidUser(): string
    {
        return $this->uuidUser;
    }

    public function setUuidUser(string $uuidUser): void
    {
        $this->uuid_user = $uuidUser;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setBusinessGroupName(string $businessGroupName): void
    {
        $this->businessGroupName = $businessGroupName;
    }

    public function getBusinessGroupName(): string
    {
        return $this->businessGroupName;
    }

    public function setClientName(string $name): void
    {
        $this->name = $name;
    }

    public function getBusinessType(): string
    {
        return $this->businessType;
    }

    public function setBusinessType(string $businessType): void
    {
        $this->businessType = $businessType;
    }

    public function getNifCif(): string
    {
        return $this->nifCif;
    }

    public function setNifCif(string $nifCif): void
    {
        $this->nifCif = $nifCif;
    }

    public function getFoundationDate(): string
    {
        return $this->foundationDate;
    }

    public function setFoundationDate(string $foundationDate): void
    {
        $this->foundationDate = $foundationDate;
    }

    public function getFiscalAddress(): string
    {
        return $this->fiscalAddress;
    }

    public function setFiscalAddress(string $fiscalAddress): void
    {
        $this->fiscalAddress = $fiscalAddress;
    }

    public function getPhysicalAddress(): string
    {
        return $this->physicalAddress;
    }

    public function setPhysicalAddress(string $physicalAddress): void
    {
        $this->physicalAddress = $physicalAddress;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getPostalCode(): int
    {
        return $this->postalCode;
    }

    public function setPostalCode(int $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function getCompanyPhone(): int
    {
        return $this->companyPhone;
    }

    public function setCompanyPhone(int $companyPhone): void
    {
        $this->companyPhone = $companyPhone;
    }

    public function getCompanyEmail(): string
    {
        return $this->companyEmail;
    }

    public function setCompanyEmail(string $companyEmail): void
    {
        $this->companyEmail = $companyEmail;
    }

    public function getNumberOfEmployees(): int
    {
        return $this->numberOfEmployees;
    }

    public function setNumberOfEmployees(int $numberOfEmployees): void
    {
        $this->numberOfEmployees = $numberOfEmployees;
    }

    public function getIndustrySector(): string
    {
        return $this->industrySector;
    }

    public function setIndustrySector(string $industrySector): void
    {
        $this->industrySector = $industrySector;
    }

    public function getAverageInventoryVolume(): int
    {
        return $this->averageInventoryVolume;
    }

    public function setAverageInventoryVolume(int $averageInventoryVolume): void
    {
        $this->averageInventoryVolume = $averageInventoryVolume;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getNumberWarehouses(): int
    {
        return $this->numberWarehouses;
    }

    public function setNumberWarehouses(int $numberWarehouses): void
    {
        $this->numberWarehouses = $numberWarehouses;
    }

    public function getAnnualSalesVolume(): string
    {
        return $this->annualSalesVolume;
    }

    public function setAnnualSalesVolume(string $annualSalesVolume): void
    {
        $this->annualSalesVolume = $annualSalesVolume;
    }
}
