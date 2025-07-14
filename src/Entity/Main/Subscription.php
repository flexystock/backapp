<?php

namespace App\Entity\Main;
use App\Entity\Main\Client;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'subscription')]
class Subscription
{
    #[ORM\Id]
    #[ORM\Column(name: 'uuid_subscription', type: 'string', length: 36)]
    private string $uuidSubscription;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'subscriptions')]
    #[ORM\JoinColumn(name: 'client_uuid', referencedColumnName: 'uuid_client')]
    private Client $client;

    #[ORM\ManyToOne(targetEntity: SubscriptionPlan::class)]
    #[ORM\JoinColumn(name: 'subscription_plan_id', referencedColumnName: 'id')]
    private SubscriptionPlan $plan;

    #[ORM\Column(name: 'started_at', type: 'datetime')]
    private \DateTimeInterface $startedAt;

    #[ORM\Column(name: 'ended_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $endedAt = null;

    #[ORM\Column(name: 'is_active', type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(name: 'uuid_user_creation', type: 'string', length: 36, nullable: true)]
    private ?string $uuidUserCreation = null;

    #[ORM\Column(name: 'uuid_user_modification', type: 'string', length: 36, nullable: true)]
    private ?string $uuidUserModification = null;

    #[ORM\OneToMany(mappedBy: 'subscription', targetEntity: RentedScale::class)]
    private Collection $rentedScales;

    public function __construct()
    {
        $this->rentedScales = new ArrayCollection();
    }

    public function getUuidSubscription(): string
    {
        return $this->uuidSubscription;
    }

    public function setUuidSubscription(string $uuidSubscription): self
    {
        $this->uuidSubscription = $uuidSubscription;
        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getPlan(): SubscriptionPlan
    {
        return $this->plan;
    }

    public function setPlan(SubscriptionPlan $plan): self
    {
        $this->plan = $plan;
        return $this;
    }

    public function getStartedAt(): \DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->endedAt;
    }

    public function setEndedAt(?\DateTimeInterface $endedAt): self
    {
        $this->endedAt = $endedAt;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getRentedScales(): Collection
    {
        return $this->rentedScales;
    }

    public function addRentedScale(RentedScale $rentedScale): self
    {
        if (!$this->rentedScales->contains($rentedScale)) {
            $this->rentedScales[] = $rentedScale;
            $rentedScale->setSubscription($this);
        }
        return $this;
    }

    public function removeRentedScale(RentedScale $rentedScale): self
    {
        $this->rentedScales->removeElement($rentedScale);
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
}