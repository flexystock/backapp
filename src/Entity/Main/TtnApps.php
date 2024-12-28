<?php

namespace App\Entity\Main;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'ttn_apps')]
class TtnApps
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: 'string', length: 36)]
    private string $uuid_client;

    #[ORM\Column(type: 'string', length: 100)]
    private string $ttn_application_id;

    #[ORM\Column(type: 'string', length: 100)]
    private string $ttn_application_name;

    #[ORM\Column(type: 'string', length: 255)]
    private string $ttn_application_description;

    #[ORM\Column(type: 'string', length: 100)]
    private string $network_server_address;

    #[ORM\Column(type: 'string', length: 100)]
    private string $application_server_address;

    #[ORM\Column(type: 'string', length: 100)]
    private string $join_server_address;

    #[ORM\Column(type: 'string', length: 100)]
    private string $ttn_application_key_id;

    #[ORM\Column(type: 'string', length: 100)]
    private string $ttn_application_key;

    #[ORM\Column(type: 'string', length: 100)]
    private string $ttn_application_key_name;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $expires_ttn_application_key;

    #[ORM\Column(type: 'string', length: 36)]
    private string $uuid_user_creation;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $datehour_creation;

    #[ORM\Column(type: 'string', length: 36)]
    private string $uuid_user_modification;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $datehour_modification;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuidClient(): ?string
    {
        return $this->uuid_client;
    }

    public function setUuidClient(string $uuid_client): self
    {
        $this->uuid_client = $uuid_client;

        return $this;
    }

    public function getTtnApplicationId(): ?string
    {
        return $this->ttn_application_id;
    }

    public function setTtnApplicationId(string $ttn_application_id): self
    {
        $this->ttn_application_id = $ttn_application_id;

        return $this;
    }

    public function getTtnApplicationName(): ?string
    {
        return $this->ttn_application_name;
    }

    public function setTtnApplicationName(string $ttn_application_name): self
    {
        $this->ttn_application_name = $ttn_application_name;

        return $this;
    }

    public function getTtnApplicationDescription(): ?string
    {
        return $this->ttn_application_description;
    }

    public function setTtnApplicationDescription(string $ttn_application_description): self
    {
        $this->ttn_application_description = $ttn_application_description;

        return $this;
    }

    public function getNetworkServerAddress(): ?string
    {
        return $this->network_server_address;
    }

    public function setNetworkServerAddress(string $network_server_address): self
    {
        $this->network_server_address = $network_server_address;

        return $this;
    }

    public function getApplicationServerAddress(): ?string
    {
        return $this->application_server_address;
    }

    public function setApplicationServerAddress(string $application_server_address): self
    {
        $this->application_server_address = $application_server_address;

        return $this;
    }

    public function getJoinServerAddress(): ?string
    {
        return $this->join_server_address;
    }

    public function setJoinServerAddress(string $join_server_address): self
    {
        $this->join_server_address = $join_server_address;

        return $this;
    }

    public function getTtnApplicationKeyId(): ?string
    {
        return $this->ttn_application_key_id;
    }

    public function setTtnApplicationKeyId(string $ttn_application_key_id): self
    {
        $this->ttn_application_key_id = $ttn_application_key_id;

        return $this;
    }

    public function getTtnApplicationKey(): ?string
    {
        return $this->ttn_application_key;
    }

    public function setTtnApplicationKey(string $ttn_application_key): self
    {
        $this->ttn_application_key = $ttn_application_key;

        return $this;
    }

    public function getTtnApplicationKeyName(): ?string
    {
        return $this->ttn_application_key_name;
    }

    public function setTtnApplicationKeyName(string $ttn_application_key_name): self
    {
        $this->ttn_application_key_name = $ttn_application_key_name;

        return $this;
    }

    public function getExpiresTtnApplicationKey(): ?\DateTimeInterface
    {
        return $this->expires_ttn_application_key;
    }

    public function setExpiresTtnApplicationKey(\DateTimeInterface $expires_ttn_application_key): self
    {
        $this->expires_ttn_application_key = $expires_ttn_application_key;

        return $this;
    }

    public function getUuidUserCreation(): ?string
    {
        return $this->uuid_user_creation;
    }

    public function setUuidUserCreation(string $uuid_user_creation): self
    {
        $this->uuid_user_creation = $uuid_user_creation;

        return $this;
    }

    public function getDatehourCreation(): ?\DateTimeInterface
    {
        return $this->datehour_creation;
    }

    public function setDatehourCreation(\DateTimeInterface $datehour_creation): self
    {
        $this->datehour_creation = $datehour_creation;

        return $this;
    }

    public function getUuidUserModification(): ?string
    {
        return $this->uuid_user_modification;
    }

    public function setUuidUserModification(string $uuid_user_modification): self
    {
        $this->uuid_user_modification = $uuid_user_modification;

        return $this;
    }

    public function getDatehourModification(): ?\DateTimeInterface
    {
        return $this->datehour_modification;
    }

    public function setDatehourModification(\DateTimeInterface $datehour_modification): self
    {
        $this->datehour_modification = $datehour_modification;

        return $this;
    }
}


