<?php
declare(strict_types=1);
namespace App\Product\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: App\Product\Infrastructure\OutputAdapters\ProductRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['id'])]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

}
