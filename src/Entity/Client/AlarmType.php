<?php
// src/Entity/Client/AlarmType.php
namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'alarm_types')]
class AlarmType
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(type: 'string', length: 50)]
    private string $type_name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    // Getters y Setters

    public function getId(): int
    {
        return $this->id;
    }

    public function getTypeName(): string
    {
        return $this->type_name;
    }

    public function setTypeName(string $typeName): self
    {
        $this->type_name = $typeName;
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
}
