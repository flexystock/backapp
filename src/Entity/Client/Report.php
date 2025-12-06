<?php

namespace App\Entity\Client;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'report')]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 150)]
    private string $name;

    #[ORM\Column(type: 'string', length: 50)]
    private string $period;

    #[ORM\Column(name: 'send_time', type: Types::TIME_MUTABLE)]
    private \DateTimeInterface $sendTime;

    #[ORM\Column(name: 'report_type', type: 'string', length: 50)]
    private string $reportType;

    #[ORM\Column(name: 'product_filter', type: 'string', length: 255, nullable: true)]
    private ?string $productFilter = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $email;

    #[ORM\Column(name: 'uuid_user_creation', type: 'string', length: 36)]
    private string $uuidUserCreation;

    #[ORM\Column(name: 'datehour_creation', type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $datehourCreation;

    #[ORM\Column(name: 'uuid_user_modification', type: 'string', length: 36, nullable: true)]
    private ?string $uuidUserModification = null;

    #[ORM\Column(name: 'datehour_modification', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datehourModification = null;

    /**
     * Relación con productos específicos
     * @var Collection<int, ReportProduct>
     */
    #[ORM\OneToMany(mappedBy: 'report', targetEntity: ReportProduct::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $reportProducts;

    public function __construct()
    {
        $this->reportProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPeriod(): string
    {
        return $this->period;
    }

    public function setPeriod(string $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function getSendTime(): \DateTimeInterface
    {
        return $this->sendTime;
    }

    public function setSendTime(\DateTimeInterface $sendTime): self
    {
        $this->sendTime = $sendTime;

        return $this;
    }

    public function getReportType(): string
    {
        return $this->reportType;
    }

    public function setReportType(string $reportType): self
    {
        $this->reportType = $reportType;

        return $this;
    }

    public function getProductFilter(): ?string
    {
        return $this->productFilter;
    }

    public function setProductFilter(?string $productFilter): self
    {
        $this->productFilter = $productFilter;

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

    public function getUuidUserCreation(): string
    {
        return $this->uuidUserCreation;
    }

    public function setUuidUserCreation(string $uuidUserCreation): self
    {
        $this->uuidUserCreation = $uuidUserCreation;

        return $this;
    }

    public function getDatehourCreation(): \DateTimeInterface
    {
        return $this->datehourCreation;
    }

    public function setDatehourCreation(\DateTimeInterface $datehourCreation): self
    {
        $this->datehourCreation = $datehourCreation;

        return $this;
    }

    public function getUuidUserModification(): ?string
    {
        return $this->uuidUserModification;
    }

    public function setUuidUserModification(?string $uuidUserModification): self
    {
        $this->uuidUserModification = $uuidUserModification;

        return $this;
    }

    public function getDatehourModification(): ?\DateTimeInterface
    {
        return $this->datehourModification;
    }

    public function setDatehourModification(?\DateTimeInterface $datehourModification): self
    {
        $this->datehourModification = $datehourModification;

        return $this;
    }

    /**
     * @return Collection<int, ReportProduct>
     */
    public function getReportProducts(): Collection
    {
        return $this->reportProducts;
    }

    public function addReportProduct(ReportProduct $reportProduct): self
    {
        if (!$this->reportProducts->contains($reportProduct)) {
            $this->reportProducts->add($reportProduct);
            $reportProduct->setReport($this);
        }

        return $this;
    }

    public function removeReportProduct(ReportProduct $reportProduct): self
    {
        if ($this->reportProducts->removeElement($reportProduct)) {
            if ($reportProduct->getReport() === $this) {
                $reportProduct->setReport(null);
            }
        }

        return $this;
    }

    /**
     * Obtiene los IDs de productos asociados a este informe
     *
     * @return array<int>
     */
    public function getProductIds(): array
    {
        return $this->reportProducts
            ->map(fn (ReportProduct $rp) => $rp->getProduct()->getId())
            ->toArray();
    }

    /**
     * Obtiene los productos asociados a este informe
     *
     * @return array<Product>
     */
    public function getProducts(): array
    {
        return $this->reportProducts
            ->map(fn (ReportProduct $rp) => $rp->getProduct())
            ->toArray();
    }

    /**
     * Limpia todos los productos asociados
     */
    public function clearProducts(): self
    {
        $this->reportProducts->clear();

        return $this;
    }

    /**
     * Añade un producto al informe
     */
    public function addProduct(Product $product): self
    {
        $reportProduct = new ReportProduct($this, $product);
        $this->addReportProduct($reportProduct);

        return $this;
    }

    /**
     * Verifica si el filtro es de productos específicos
     * Compatibilidad: chequea tanto productFilter como la presencia de reportProducts
     */
    public function hasSpecificProducts(): bool
    {
        return $this->productFilter === 'specific' || !$this->reportProducts->isEmpty();
    }
}