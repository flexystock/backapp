<?php

namespace App\Client\Application\DTO;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateClientRequest
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

    #[Assert\NotBlank(message: 'El nombre de la empresa es obligatorio.')]
    #[Assert\Length(
        max: 255,
    )]
    #[SerializedName('company_name')]
    private string $business_group_name;

    #[Assert\NotBlank(message: 'El tipo de negocio es obligatorio.')]
    #[Assert\Length(
        max: 255,
    )]
    private string $business_type;

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
    private string $physical_address;

    #[Assert\NotBlank(message: 'La ciudad es obligatorio.')]
    #[Assert\Length(
        max: 255,
    )]
    private string $city;

    #[Assert\NotBlank(message: 'El pais es obligatorio.')]
    #[Assert\Length(
        max: 255,
    )]
    private string $country;

    #[Assert\NotBlank(message: 'El codigo postal es obligatorio.')]
    private int $postal_code;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric', message: 'El número de teléfono debe ser numérico.')]
    #[Assert\Length(
        min: 9,
        max: 15,
        minMessage: 'El número de teléfono debe tener al menos {{ limit }} dígitos.',
        maxMessage: 'El número de teléfono no puede tener más de {{ limit }} dígitos.'
    )]
    private int $company_phone;

    #[Assert\NotBlank(message: 'El email de la compañia es obligatorio.')]
    #[Assert\Email(message: "El correo electrónico '{{ value }}' no es válido.")]
    private string $company_email;

    #[Assert\NotBlank(message: 'El numero de empleados es obligatorio.')]
    private int $number_of_employees;

    #[Assert\NotBlank(message: 'El sector industrial es obligatorio.')]
    private string $industry_sector;

    #[Assert\NotBlank(message: 'El volumen de inevntario es obligatorio.')]
    private int $average_inventory_volume;

    #[Assert\NotBlank(message: 'El tipo de moneda es obligatorio.')]
    private string $currency;

    #[Assert\NotBlank(message: 'El tipo de metodo de pago es obligatorio.')]
    private string $preferred_payment_methods;

    #[Assert\NotBlank(message: 'El horario es obligatorio.')]
    private string $operation_hours;

    #[Assert\NotBlank(message: 'Indique si tiene mas de un alamacen.')]
    private string $has_multiple_warehouses;

    #[Assert\NotBlank(message: 'El volumen anual de ventas es obligatorio.')]
    private string $annual_sales_volume;

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

    public function getPreferredPaymentMethods(): string
    {
        return $this->preferred_payment_methods;
    }

    public function setPreferredPaymentMethods(string $preferred_payment_methods): void
    {
        $this->preferred_payment_methods = $preferred_payment_methods;
    }

    public function getOperationHours(): string
    {
        return $this->operation_hours;
    }

    public function setOperationHours(string $operation_hours): void
    {
        $this->operation_hours = $operation_hours;
    }

    public function getHasMultipleWarehouses(): string
    {
        return $this->has_multiple_warehouses;
    }

    public function setHasMultipleWarehouses(string $has_multiple_warehouses): void
    {
        $this->has_multiple_warehouses = $has_multiple_warehouses;
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
