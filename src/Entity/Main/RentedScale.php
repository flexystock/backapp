<?php

namespace App\Entity\Main;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'rented_scale')]
class RentedScale
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Subscription::class, inversedBy: 'rentedScales')]
    #[ORM\JoinColumn(name: 'subscription_uuid', referencedColumnName: 'uuid_subscription')]
    private Subscription $subscription;

    #[ORM\Column(name: 'scale_uuid', type: 'string', length: 36)]
    private string $scaleUuid;

    #[ORM\Column(name: 'rented_at', type: 'datetime')]
    private \DateTimeInterface $rentedAt;

    #[ORM\Column(name: 'returned_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $returnedAt = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(Subscription $subscription): self
    {
        $this->subscription = $subscription;
        return $this;
    }

    public function getScaleUuid(): string
    {
        return $this->scaleUuid;
    }

    public function setScaleUuid(string $scaleUuid): self
    {
        $this->scaleUuid = $scaleUuid;
        return $this;
    }

    public function getRentedAt(): \DateTimeInterface
    {
        return $this->rentedAt;
    }

    public function setRentedAt(\DateTimeInterface $rentedAt): self
    {
        $this->rentedAt = $rentedAt;
        return $this;
    }

    public function getReturnedAt(): ?\DateTimeInterface
    {
        return $this->returnedAt;
    }

    public function setReturnedAt(?\DateTimeInterface $returnedAt): self
    {
        $this->returnedAt = $returnedAt;
        return $this;
    }
}