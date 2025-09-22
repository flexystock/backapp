<?php

namespace App\Client\Application\DTO;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateInfoClientRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_UUID')]
    #[Assert\Uuid]
    private string $uuidClient;

    #[Assert\NotBlank(message: 'REQUIRED_NAME')]
    #[Assert\Length(
        max: 255,
    )]
    #[SerializedName('name')]
    private string $name;

    #[Assert\NotBlank(message: 'REQUIRED_NIF')]
    #[Assert\Length(
        max: 12,
    )]
    private string $nifCif;

    #[Assert\NotBlank(message: 'REQUIRED_FISCAL_ADDRESS')]
    #[Assert\Length(
        max: 255,
    )]
    private string $fiscalAddress;

    #[Assert\NotBlank(message: 'REQUIRED_PHYSICAL_ADDRESS')]
    #[Assert\Length(
        max: 255,
    )]
    private string $physicalAddress;

    #[Assert\NotBlank(message: 'REQUIRED_CITY')]
    #[Assert\Length(
        max: 255,
    )]
    private string $city;

    #[Assert\NotBlank(message: 'REQUIRED_COUNTRY')]
    #[Assert\Length(
        max: 255,
    )]
    private string $country;

    #[Assert\NotBlank(message: 'REQUIRED_POSTAL_CODE')]
    private int $postalCode;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'string', message: 'INVALID_PHONE_NUMBER')]
    private string $companyPhone;

    #[Assert\NotBlank(message: 'COMPANY_EMAIL_REQUIRED')]
    #[Assert\Email(message: 'INVALID_EMAIL')]
    private string $companyEmail;

    private string $uuidUserModification;
    private \DateTimeInterface $datehourModification;

    public function __construct(
        string $uuidClient,
        string $name,
        string $nifCif,
        string $fiscalAddress,
        string $physicalAddress,
        string $city,
        string $country,
        int $postalCode,
        string $companyPhone,
        string $companyEmail,
    ) {
        $this->uuidClient = $uuidClient;
        $this->name = $name;
        $this->nifCif = $nifCif;
        $this->fiscalAddress = $fiscalAddress;
        $this->physicalAddress = $physicalAddress;
        $this->city = $city;
        $this->country = $country;
        $this->postalCode = $postalCode;
        $this->companyPhone = $companyPhone;
        $this->companyEmail = $companyEmail;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
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

    public function getUuidUserModification(): string
    {
        return $this->uuidUserModification;
    }

    public function getDatehourModification(): \DateTimeInterface
    {
        return $this->datehourModification;
    }

    public function setDatehourModification(\DateTimeInterface $datehourModification): void
    {
        $this->datehourModification = $datehourModification;
    }

    public function setUuidUserModification(string $uuidUserModification): void
    {
        $this->uuidUserModification = $uuidUserModification;
    }
}
