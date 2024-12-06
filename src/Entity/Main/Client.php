<?php

namespace App\Entity\Main;

use App\Client\Infrastructure\OutputAdapters\Repositories\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\Table(name: 'client')]
class Client
{
    #[ORM\Id]
    #[ORM\Column(name: 'uuid_client', type: 'string', length: 36, unique: true)]
    private string $uuid_client;

    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(name: 'uuid_business_group', type: 'string', length: 36, nullable: true)]
    private ?string $uuid_business_group = null;

    #[ORM\Column(name: 'database_name', type: 'string', length: 255, nullable: true)]
    private ?string $database_name = null;

    #[ORM\Column(name: 'client_name', type: 'string', length: 255)]
    private string $client_name;

    #[ORM\Column(name: 'host', type: 'string', length: 255, nullable: true)]
    private ?string $host = null;

    #[ORM\Column(name: 'port_bbdd', type: 'integer', nullable: true)]
    private ?int $port_bbdd = null;

    #[ORM\Column(name: 'database_user_name', type: 'string', length: 255, nullable: true)]
    private ?string $database_user_name = null;

    #[ORM\Column(name: 'database_password', type: 'string', length: 255, nullable: true)]
    private ?string $database_password = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(name: 'container_name', type: 'string', length: 255, nullable: true)]
    private ?string $container_name = null;

    #[ORM\Column(name: 'docker_volume_name', type: 'string', length: 255, nullable: true)]
    private ?string $docker_volume_name = null;

    // Campos adicionales
    #[ORM\Column(name: 'business_type', type: 'string', length: 100)]
    private string $business_type;

    #[ORM\Column(name: 'nif_cif', type: 'string', length: 50, nullable: true)]
    private ?string $nif_cif = null;

    #[ORM\Column(name: 'foundation_date', type: 'date', nullable: true)]
    private ?\DateTimeInterface $foundation_date = null;

    #[ORM\Column(name: 'fiscal_address', type: 'string', length: 255)]
    private string $fiscal_address;

    #[ORM\Column(name: 'physical_address', type: 'string', length: 255, nullable: true)]
    private ?string $physical_address = null;

    #[ORM\Column(name: 'city', type: 'string', length: 100)]
    private string $city;

    #[ORM\Column(name: 'country', type: 'string', length: 100)]
    private string $country;

    #[ORM\Column(name: 'postal_code', type: 'string', length: 20)]
    private string $postal_code;

    #[ORM\Column(name: 'company_phone', type: 'string', length: 20)]
    private string $company_phone;

    #[ORM\Column(name: 'company_email', type: 'string', length: 255, nullable: true)]
    private ?string $company_email = null;

    #[ORM\Column(name: 'number_of_employees', type: 'integer', nullable: true)]
    private ?int $number_of_employees = null;

    #[ORM\Column(name: 'industry_sector', type: 'string', length: 100, nullable: true)]
    private ?string $industry_sector = null;

    #[ORM\Column(name: 'average_inventory_volume', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $average_inventory_volume = null;

    #[ORM\Column(name: 'currency', type: 'string', length: 10, nullable: true)]
    private ?string $currency = null;

    #[ORM\Column(name: 'preferred_payment_methods', type: 'string', length: 255, nullable: true)]
    private ?string $preferred_payment_methods = null;

    #[ORM\Column(name: 'operation_hours', type: 'string', length: 255, nullable: true)]
    private ?string $operation_hours = null;

    #[ORM\Column(name: 'has_multiple_warehouses', type: 'boolean', options: ['default' => false])]
    private bool $has_multiple_warehouses = false;

    #[ORM\Column(name: 'annual_sales_volume', type: 'decimal', precision: 15, scale: 2, nullable: true)]
    private ?string $annual_sales_volume = null;

    #[ORM\Column(name: 'verification_token', type: 'string', length: 255, nullable: true)]
    private ?string $verification_token = null;

    #[ORM\Column(name: 'verification_token_expires_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $verification_token_expires_at = null;

    #[ORM\Column(name: 'is_verified', type: 'boolean', options: ['default' => false])]
    private bool $is_verified = false;

    // Relaciones
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'clients')]
    private Collection $users;

    // Constructor
    public function __construct()
    {
        $this->uuid_client = Uuid::v4()->toRfc4122();
        $this->users = new ArrayCollection();
    }

    public function getUuidClient(): string
    {
        return $this->uuid_client;
    }

    public function setUuidClient(string $uuid_client): self
    {
        $this->uuid_client = $uuid_client;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUuidBusinessGroup(): ?string
    {
        return $this->uuid_business_group;
    }

    public function setUuidBusinessGroup(?string $uuid_business_group): void
    {
        $this->uuid_business_group = $uuid_business_group;
    }

    public function getDatabaseName(): ?string
    {
        return $this->database_name;
    }

    public function setDatabaseName(?string $database_name): void
    {
        $this->database_name = $database_name;
    }

    public function getClientName(): string
    {
        return $this->client_name;
    }

    public function setClientName(string $client_name): void
    {
        $this->client_name = $client_name;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(?string $host): void
    {
        $this->host = $host;
    }

    public function getPortBbdd(): ?int
    {
        return $this->port_bbdd;
    }

    public function setPortBbdd(?int $port_bbdd): void
    {
        $this->port_bbdd = $port_bbdd;
    }

    public function getDatabaseUserName(): ?string
    {
        return $this->database_user_name;
    }

    public function setDatabaseUserName(?string $database_user_name): void
    {
        $this->database_user_name = $database_user_name;
    }

    public function getDatabasePassword(): ?string
    {
        return $this->database_password;
    }

    public function setDatabasePassword(?string $database_password): void
    {
        $this->database_password = $database_password;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function getContainerName(): ?string
    {
        return $this->container_name;
    }

    public function setContainerName(?string $container_name): void
    {
        $this->container_name = $container_name;
    }

    public function getDockerVolumeName(): ?string
    {
        return $this->docker_volume_name;
    }

    public function setDockerVolumeName(?string $docker_volume_name): void
    {
        $this->docker_volume_name = $docker_volume_name;
    }

    public function getBusinessType(): string
    {
        return $this->business_type;
    }

    public function setBusinessType(string $business_type): void
    {
        $this->business_type = $business_type;
    }

    public function getNifCif(): ?string
    {
        return $this->nif_cif;
    }

    public function setNifCif(?string $nif_cif): void
    {
        $this->nif_cif = $nif_cif;
    }

    public function getFoundationDate(): ?\DateTimeInterface
    {
        return $this->foundation_date;
    }

    public function setFoundationDate(?\DateTimeInterface $foundation_date): void
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

    public function getPhysicalAddress(): ?string
    {
        return $this->physical_address;
    }

    public function setPhysicalAddress(?string $physical_address): void
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

    public function getPostalCode(): string
    {
        return $this->postal_code;
    }

    public function setPostalCode(string $postal_code): void
    {
        $this->postal_code = $postal_code;
    }

    public function getCompanyPhone(): string
    {
        return $this->company_phone;
    }

    public function setCompanyPhone(string $company_phone): void
    {
        $this->company_phone = $company_phone;
    }

    public function getCompanyEmail(): ?string
    {
        return $this->company_email;
    }

    public function setCompanyEmail(?string $company_email): void
    {
        $this->company_email = $company_email;
    }

    public function getNumberOfEmployees(): ?int
    {
        return $this->number_of_employees;
    }

    public function setNumberOfEmployees(?int $number_of_employees): void
    {
        $this->number_of_employees = $number_of_employees;
    }

    public function getIndustrySector(): ?string
    {
        return $this->industry_sector;
    }

    public function setIndustrySector(?string $industry_sector): void
    {
        $this->industry_sector = $industry_sector;
    }

    public function getAverageInventoryVolume(): ?string
    {
        return $this->average_inventory_volume;
    }

    public function setAverageInventoryVolume(?string $average_inventory_volume): void
    {
        $this->average_inventory_volume = $average_inventory_volume;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    public function getPreferredPaymentMethods(): ?string
    {
        return $this->preferred_payment_methods;
    }

    public function setPreferredPaymentMethods(?string $preferred_payment_methods): void
    {
        $this->preferred_payment_methods = $preferred_payment_methods;
    }

    public function getOperationHours(): ?string
    {
        return $this->operation_hours;
    }

    public function setOperationHours(?string $operation_hours): void
    {
        $this->operation_hours = $operation_hours;
    }

    public function isHasMultipleWarehouses(): bool
    {
        return $this->has_multiple_warehouses;
    }

    public function setHasMultipleWarehouses(bool $has_multiple_warehouses): void
    {
        $this->has_multiple_warehouses = $has_multiple_warehouses;
    }

    public function getAnnualSalesVolume(): ?string
    {
        return $this->annual_sales_volume;
    }

    public function setAnnualSalesVolume(?string $annual_sales_volume): void
    {
        $this->annual_sales_volume = $annual_sales_volume;
    }

    public function getVerificationToken(): ?string
    {
        return $this->verification_token;
    }

    public function setVerificationToken(?string $verification_token): void
    {
        $this->verification_token = $verification_token;
    }

    public function getVerificationTokenExpiresAt(): ?\DateTimeInterface
    {
        return $this->verification_token_expires_at;
    }

    public function setVerificationTokenExpiresAt(?\DateTimeInterface $verification_token_expires_at): void
    {
        $this->verification_token_expires_at = $verification_token_expires_at;
    }

    public function isIsVerified(): bool
    {
        return $this->is_verified;
    }

    public function setIsVerified(bool $is_verified): void
    {
        $this->is_verified = $is_verified;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function setUsers(Collection $users): void
    {
        $this->users = $users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    // Método para eliminar un usuario de la colección
    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeClient($this);
        }

        return $this;
    }

    public function setDockVolumeName(string $volumeName): void
    {
        $this->docker_volume_name = $volumeName;
    }
}
