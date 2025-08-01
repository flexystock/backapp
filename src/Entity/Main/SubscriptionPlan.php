<?php

namespace App\Entity\Main;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'subscription_plan')]
class SubscriptionPlan
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $price;

    #[ORM\Column(name: 'stripe_price_id', type: 'string', length: 100, nullable: true)]
    private ?string $stripePriceId = null;

    #[ORM\Column(name: 'max_scales', type: 'integer', options: ['unsigned' => true])]
    private int $maxScales;

    #[ORM\Column(name: 'uuid_user_creation', type: 'string', length: 36, nullable: true)]
    private ?string $uuidUserCreation = null;

    #[ORM\Column(name: 'uuid_user_modification', type: 'string', length: 36, nullable: true)]
    private ?string $uuidUserModification = null;

    #[ORM\Column(name: 'datehour_creation', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $datehourCreation = null;

    #[ORM\Column(name: 'datehour_modification', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $datehourModification = null;

    public function getId(): int
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getMaxScales(): int
    {
        return $this->maxScales;
    }

    public function setMaxScales(int $maxScales): self
    {
        $this->maxScales = $maxScales;
        return $this;
    }

    public function getUuidUserCreation(): ?string
    {
        return $this->uuidUserCreation;
    }

    public function setUuidUserCreation(?string $uuidUserCreation): self
    {
        $this->uuidUserCreation = $uuidUserCreation;
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

    public function getDatehourCreation(): ?\DateTimeInterface
    {
        return $this->datehourCreation;
    }

    public function setDatehourCreation(?\DateTimeInterface $datehourCreation): self
    {
        $this->datehourCreation = $datehourCreation;
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

    public function getStripePriceId(): ?string
    {
        return $this->stripePriceId;
    }

    public function setStripePriceId(?string $stripePriceId): self
    {
        $this->stripePriceId = $stripePriceId;
        return $this;
    }
}