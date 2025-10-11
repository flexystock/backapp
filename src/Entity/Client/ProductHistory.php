<?php

namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'product_history')]
class ProductHistory
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(type: 'string', length: 36)]
    private string $uuid_product;

    #[ORM\Column(type: 'string', length: 36)]
    private string $uuid_user_modification;

    #[ORM\Column(type: 'text')]
    private string $data_product_before_modification;

    #[ORM\Column(type: 'text')]
    private string $data_product_after_modification;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $date_modification;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUuidProduct(): string
    {
        return $this->uuid_product;
    }

    public function setUuidProduct(string $uuidProduct): void
    {
        $this->uuid_product = $uuidProduct;
    }

    public function getUuidUserModification(): string
    {
        return $this->uuid_user_modification;
    }

    public function setUuidUserModification(string $uuidUserModification): void
    {
        $this->uuid_user_modification = $uuidUserModification;
    }

    public function getDataProductBeforeModification(): string
    {
        return $this->data_product_before_modification;
    }

    public function setDataProductBeforeModification(string $dataProductBeforeModification): void
    {
        $this->data_product_before_modification = $dataProductBeforeModification;
    }

    public function getDataProductAfterModification(): string
    {
        return $this->data_product_after_modification;
    }

    public function setDataProductAfterModification(string $dataProductAfterModification): void
    {
        $this->data_product_after_modification = $dataProductAfterModification;
    }

    public function getDateModification(): \DateTimeInterface
    {
        return $this->date_modification;
    }

    public function setDateModification(\DateTimeInterface $dateModification): void
    {
        $this->date_modification = $dateModification;
    }
}
