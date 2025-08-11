<?php

namespace App\Client\Application\DTO;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterClientRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_USER_ID')]
    #[Assert\Length(
        max: 36,
    )]
    #[SerializedName('userId')]
    private string $uuidUser;

    #[Assert\NotBlank(message: 'REQUIRED_NAME')]
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

    #[Assert\NotBlank(message: 'REQUIRED_NIFCIF')]
    #[Assert\Length(
        max: 12,
    )]
    private string $nifCif;

    private ?string $foundationDate = null;

    #[Assert\NotBlank(message: 'REQUIRED_FISCAL_ADDRESS')]
    #[Assert\Length(
        max: 255,
    )]
    private string $fiscalAddress;

    #[Assert\NotBlank(message: 'REQUIRED_PHYSICAL_ADDRESS')]
    #[Assert\Length(
        max: 255,
    )]
    #[Assert\Regex(
        pattern: '/^[A-Za-z0-9áéíóúÁÉÍÓÚñÑüÜ\.,\/\-\s]{1,100}$/u',
        message: 'INVALID_PHYSICAL_ADDRESS'
    )]
    private string $physicalAddress;

    #[Assert\NotBlank(message: 'REQUIRED_CITY')]
    #[Assert\Length(
        max: 255,
    )]
    #[Assert\Regex(
        pattern: '/^[A-Za-zÁÉÍÓÚáéíóúÑñÜüçÇàáâ...]{1,50}(?:[ -][A-Za-zÁÉÍÓÚáéíóúÑñÜüçÇàáâ...]{1,50})*$/u',
        message: 'INVALID_CITY'
    )]
    private string $city;

    #[Assert\NotBlank(message: 'REQUIRED_COUNTRY')]
    #[Assert\Length(
        max: 255,
    )]
    private string $country;

    #[Assert\NotBlank(message: 'REQUIRED_POSTAL_CODE')]
    #[Assert\Regex(
        pattern: '/^[A-Za-z0-9À-ÿ\s\-]{3,10}$/u',
        message: 'INVALID_POSTAL_CODE'
    )]
    private int $postalCode;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'string', message: 'INVALID_PHONE_NUMBER')]
    private string $companyPhone;

    #[Assert\NotBlank(message: 'COMPANY_EMAIL_REQUIRED')]
    #[Assert\Email(message: 'INVALID_EMAIL')]
    private string $companyEmail;

    #[Assert\Positive(message: 'INVALID_NUMBER_OF_EMPLOYEES')]
    #[Assert\LessThanOrEqual(
        value: 999999,
        message: 'INVALID_NUMBER_OF_EMPLOYEES'
    )]
    private ?int $numberOfEmployees = null;

    #[Assert\NotBlank(message: 'REQUIRED_INDUSTRY_SECTOR')]
    #[Assert\Regex(
        pattern: '/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü0-9\s\.,\-\&]{1,50}$/u',
        message: 'INVALID_INDUSTRY_SECTOR'
    )]
    private string $industrySector;

    #[Assert\Regex(
        pattern: '/^\d{1,10}$/',
        message: 'INVALID_AVERAGE_INVENTORY_VOLUME'
    )]
    private ?int $averageInventoryVolume = null;

    #[Assert\Regex(
        pattern: '/^([A-Za-z]{3,15}(?:\s?[A-Za-z]{2,15})?|[€$¥£₣₹₣])$/u',
        message: 'INVALID_CURRENCY'
    )]
    private ?string $currency = null;

    #[Assert\Type(type: 'int', message: 'INVALID_NUMBER_OF_WAREHOUSES')]
    private ?int $numberWarehouses = null;

    #[Assert\Regex(
        pattern: '/^\d{1,3}(?:\.\d{3})*(?:,([0-9]{1,2}))?$/',
        message: 'INVALID_ANNUAL_SALES_VOLUME'
    )]
    private ?int $annualSalesVolume = null;

    public function getUuidUser(): string
    {
        return $this->uuidUser;
    }

    public function setUuidUser(string $uuidUser): void
    {
        $this->uuidUser = $uuidUser;
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

    public function getNifCif(): string
    {
        return $this->nifCif;
    }

    public function setNifCif(string $nifCif): void
    {
        $this->nifCif = $nifCif;
    }

    public function getFoundationDate(): ?string
    {
        return $this->foundationDate;
    }

    public function setFoundationDate(?string $foundationDate): void
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

    public function getCompanyPhone(): string
    {
        return $this->companyPhone;
    }

    public function setCompanyPhone(string $companyPhone): void
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

    public function getNumberOfEmployees(): ?int
    {
        return $this->numberOfEmployees;
    }

    public function setNumberOfEmployees(?int $numberOfEmployees): void
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

    public function getAverageInventoryVolume(): ?int
    {
        return $this->averageInventoryVolume;
    }

    public function setAverageInventoryVolume(?int $averageInventoryVolume): void
    {
        $this->averageInventoryVolume = $averageInventoryVolume;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    public function getNumberWarehouses(): ?int
    {
        return $this->numberWarehouses;
    }

    public function setNumberWarehouses(?int $numberWarehouses): void
    {
        $this->numberWarehouses = $numberWarehouses;
    }

    public function getAnnualSalesVolume(): ?int
    {
        return $this->annualSalesVolume;
    }

    public function setAnnualSalesVolume(?int $annualSalesVolume): void
    {
        $this->annualSalesVolume = $annualSalesVolume;
    }
}
