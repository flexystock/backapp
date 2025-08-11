<?php

namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'scale_history')]
class ScaleHistory
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(type: 'string', length: 36)]
    private string $uuid_scale;

    #[ORM\Column(type: 'string', length: 36)]
    private string $uuid_user_modification;

    #[ORM\Column(type: 'text')]
    private string $data_scale_before_modification;

    #[ORM\Column(type: 'text')]
    private string $data_scale_after_modification;

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

    public function getUuidScale(): string
    {
        return $this->uuid_scale;
    }

    public function setUuidScale(string $uuidScale): void
    {
        $this->uuid_scale = $uuidScale;
    }

    public function getUuidUserModification(): string
    {
        return $this->uuid_user_modification;
    }

    public function setUuidUserModification(string $uuidUserModification): void
    {
        $this->uuid_user_modification = $uuidUserModification;
    }

    public function getDataScaleBeforeModification(): string
    {
        return $this->data_scale_before_modification;
    }

    public function setDataScaleBeforeModification(string $dataScaleBeforeModification): void
    {
        $this->data_scale_before_modification = $dataScaleBeforeModification;
    }

    public function getDataScaleAfterModification(): string
    {
        return $this->data_scale_after_modification;
    }

    public function setDataScaleAfterModification(string $dataScaleAfterModification): void
    {
        $this->data_scale_after_modification = $dataScaleAfterModification;
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
