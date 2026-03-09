<?php

namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'product_service_hour')]
#[ORM\HasLifecycleCallbacks]
class ProductServiceHour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Product $product;

    #[ORM\Column(name: 'day_of_week', type: 'smallint')]
    private int $dayOfWeek;

    #[ORM\Column(name: 'start_time_1', type: 'time')]
    private \DateTime $startTime1;

    #[ORM\Column(name: 'end_time_1', type: 'time')]
    private \DateTime $endTime1;

    #[ORM\Column(name: 'start_time_2', type: 'time', nullable: true)]
    private ?\DateTime $startTime2 = null;

    #[ORM\Column(name: 'end_time_2', type: 'time', nullable: true)]
    private ?\DateTime $endTime2 = null;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function coversDateTime(\DateTimeInterface $dt): bool
    {
        $time = \DateTime::createFromFormat('H:i:s', $dt->format('H:i:s'));
        if ($time === false) {
            return false;
        }
        $covers1 = $time >= $this->startTime1 && $time <= $this->endTime1;
        $covers2 = $this->startTime2 && $this->endTime2
                   && $time >= $this->startTime2 && $time <= $this->endTime2;

        return $covers1 || $covers2;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getDayOfWeek(): int
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(int $dayOfWeek): self
    {
        $this->dayOfWeek = $dayOfWeek;
        return $this;
    }

    public function getStartTime1(): \DateTime
    {
        return $this->startTime1;
    }

    public function setStartTime1(\DateTime $startTime1): self
    {
        $this->startTime1 = $startTime1;
        return $this;
    }

    public function getEndTime1(): \DateTime
    {
        return $this->endTime1;
    }

    public function setEndTime1(\DateTime $endTime1): self
    {
        $this->endTime1 = $endTime1;
        return $this;
    }

    public function getStartTime2(): ?\DateTime
    {
        return $this->startTime2;
    }

    public function setStartTime2(?\DateTime $startTime2): self
    {
        $this->startTime2 = $startTime2;
        return $this;
    }

    public function getEndTime2(): ?\DateTime
    {
        return $this->endTime2;
    }

    public function setEndTime2(?\DateTime $endTime2): self
    {
        $this->endTime2 = $endTime2;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }
}
