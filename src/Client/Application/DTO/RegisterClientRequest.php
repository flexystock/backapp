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
    #[SerializedName('user_id')]
    private string $uuid_user;

    #[Assert\NotBlank(message: 'El nombre del cliente es obligatorio.')]
    #[Assert\Length(
        max: 255,
    )]
    #[SerializedName('name')]
    private string $name;

    #[Assert\Length(
        max: 255,
    )]
    #[SerializedName('company_name')]
    private string $business_group_name;

    #[Assert\NotBlank(message: 'El NIF es obligatorio.')]
    #[Assert\Length(
        max: 12,
    )]
    private string $nif_cif;

    #[Assert\NotBlank(message: 'La fecha de creación es obligatoria.')]
    private string $foundation_date;

    #[Assert\NotBlank(message: 'La direccion fiscal es obligatorio.')]
    #[Assert\Length(
        max: 255,
    )]
    private string $fiscal_address;

    #[Assert\NotBlank(message: 'La direccion fisica es obligatorio.')]
    #[Assert\Length(
        max: 255,
    )]
    #[Assert\Regex(
        pattern: '/^[A-Za-z0-9áéíóúÁÉÍÓÚñÑüÜ\.,\/\-\s]{1,100}$/u',
        message: 'La dirección fiscal no es válida.'
    )]
    private string $physical_address;

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
    private int $postal_code;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'string', message: 'El número de teléfono debe ser numérico.')]
    private string $company_phone;

    #[Assert\NotBlank(message: 'El email de la compañia es obligatorio.')]
    #[Assert\Email(message: "El correo electrónico '{{ value }}' no es válido.")]
    private string $company_email;

    #[Assert\NotBlank(message: 'El numero de empleados es obligatorio.')]
    #[Assert\Positive(message: 'El número de empleados debe ser un número positivo.')]
    #[Assert\LessThanOrEqual(
        value: 999999,
        message: 'El número de empleados no puede exceder de 999999.'
    )]
    private int $number_of_employees;

    #[Assert\NotBlank(message: 'El sector industrial es obligatorio.')]
    #[Assert\Regex(
        pattern: '/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü0-9\s\.,\-\&]{1,50}$/u',
        message: 'El sector industrial no es válido.'
    )]
    private string $industry_sector;

    #[Assert\NotBlank(message: 'El volumen de inevntario es obligatorio.')]
    #[Assert\Regex(
        pattern: '/^\d{1,10}$/',
        message: 'El volumen promedio de inventario no es válido.'
    )]
    private int $average_inventory_volume;

    #[Assert\NotBlank(message: 'El tipo de moneda es obligatorio.')]
    #[Assert\Regex(
        pattern: '/^([A-Za-z]{3,15}(?:\s?[A-Za-z]{2,15})?|[€$¥£₣₹₣])$/u',
        message: 'La moneda no es válida.'
    )]
    private string $currency;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'int', message: 'El número de almacenes debe ser numérico.')]
    private int $number_warehouses;

    #[Assert\NotBlank(message: 'El volumen anual de ventas es obligatorio.')]
    #[Assert\Regex(
        pattern: '/^\d{1,3}(?:\.\d{3})*(?:,([0-9]{1,2}))?$/',
        message: 'El volumen anual de ventas no es válido.'
    )]
    private int $annual_sales_volume;

    public function getUuidUser(): string
    {
        return $this->uuid_user;
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
        $this->business_group_name = $businessGroupName;
    }

    public function getBusinessGroupName(): string
    {
        return $this->business_group_name;
    }

    public function setClientName(string $name): void
    {
        $this->name = $name;
    }

    public function getBusinessType(): string
    {
        return $this->business_type;
    }

    public function setBusinessType(string $business_type): void
    {
        $this->business_type = $business_type;
    }

    public function getNifCif(): string
    {
        return $this->nif_cif;
    }

    public function setNifCif(string $nif_cif): void
    {
        $this->nif_cif = $nif_cif;
    }

    public function getFoundationDate(): string
    {
        return $this->foundation_date;
    }

    public function setFoundationDate(string $foundation_date): void
    {
        $this->foundation_date = $foundation_date;
    }

    public function getFiscalAddress(): string
    {
        return $this->fiscal_address;
    }

    public function setFiscalAddress(string $fiscal_address): void
    {
        $this->fiscal_address = $fiscal_address;
    }

    public function getPhysicalAddress(): string
    {
        return $this->physical_address;
    }

    public function setPhysicalAddress(string $physical_address): void
    {
        $this->physical_address = $physical_address;
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
        return $this->postal_code;
    }

    public function setPostalCode(int $postal_code): void
    {
        $this->postal_code = $postal_code;
    }

    public function getCompanyPhone(): int
    {
        return $this->company_phone;
    }

    public function setCompanyPhone(int $company_phone): void
    {
        $this->company_phone = $company_phone;
    }

    public function getCompanyEmail(): string
    {
        return $this->company_email;
    }

    public function setCompanyEmail(string $company_email): void
    {
        $this->company_email = $company_email;
    }

    public function getNumberOfEmployees(): int
    {
        return $this->number_of_employees;
    }

    public function setNumberOfEmployees(int $number_of_employees): void
    {
        $this->number_of_employees = $number_of_employees;
    }

    public function getIndustrySector(): string
    {
        return $this->industry_sector;
    }

    public function setIndustrySector(string $industry_sector): void
    {
        $this->industry_sector = $industry_sector;
    }

    public function getAverageInventoryVolume(): int
    {
        return $this->average_inventory_volume;
    }

    public function setAverageInventoryVolume(int $average_inventory_volume): void
    {
        $this->average_inventory_volume = $average_inventory_volume;
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
        return $this->number_warehouses;
    }

    public function setNumberWarehouses(int $number_warehouses): void
    {
        $this->number_warehouses = $number_warehouses;
    }

    public function getAnnualSalesVolume(): string
    {
        return $this->annual_sales_volume;
    }

    public function setAnnualSalesVolume(string $annual_sales_volume): void
    {
        $this->annual_sales_volume = $annual_sales_volume;
    }
}
