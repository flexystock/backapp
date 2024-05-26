<?php
declare(strict_types=1);

namespace App\Entity\Main;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: App\Repository\UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'mail_UNIQUE', columns: ['mail'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true)]
    private string $uuid;

    #[ORM\Column(type: 'string', length: 50)]
    private string $name;

    #[ORM\Column(type: 'string', length: 50)]
    private string $surnames;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $mail;

    #[ORM\Column(type: 'string', length: 200)]
    private string $pass;

    #[ORM\Column(type: 'boolean')]
    private bool $isRoot = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isGhost = false;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $minutesSessionExpiration = 60;

    #[ORM\Column(type: 'boolean')]
    private bool $active = true;

    #[ORM\Column(type: 'smallint')]
    private int $failedAttempts = 0;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lockedUntil = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastAccess = null;

    #[ORM\Column(type: 'string', length: 36)]
    private string $uuidUserCreation;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $datehourCreation;

    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $uuidUserModification = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $datehourModification = null;
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
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
    public function getSurnames(): string
    {
        return $this->surnames;
    }
    public function setSurnames(string $surnames): self
    {
        $this->surnames = $surnames;
        return $this;
    }
    public function getEmail(): string
    {
        return $this->mail;
    }
    public function setEmail(string $email): self
    {
        $this->mail = $email;
        return $this;
    }
    public function getPassword(): string
    {
        return $this->pass;
    }
    public function setPassword(string $password): self
    {
        $this->pass = $password;
        return $this;
    }
    public function getUserIdentifier(): string
    {
        return $this->mail;
    }
    public function getRoles(): array
    {
        // Define los roles de usuario aquí. Por ejemplo:
        return ['ROLE_USER'];
    }
    public function eraseCredentials(): void
    {
        // Si almacenas datos sensibles temporales, límpialos aquí.
    }
    public function isRoot(): bool
    {
        return $this->isRoot;
    }
    public function setIsRoot(bool $isRoot): self
    {
        $this->isRoot = $isRoot;
        return $this;
    }
    public function isGhost(): bool
    {
        return $this->isGhost;
    }
    public function setIsGhost(bool $isGhost): self
    {
        $this->isGhost = $isGhost;
        return $this;
    }
    public function getMinutesSessionExpiration(): ?int
    {
        return $this->minutesSessionExpiration;
    }
    public function setMinutesSessionExpiration(?int $minutesSessionExpiration): self
    {
        $this->minutesSessionExpiration = $minutesSessionExpiration;
        return $this;
    }
    public function isActive(): bool
    {
        return $this->active;
    }
    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }
    public function getFailedAttempts(): int
    {
        return $this->failedAttempts;
    }
    public function setFailedAttempts(int $failedAttempts): self
    {
        $this->failedAttempts = $failedAttempts;
        return $this;
    }
    public function getLockedUntil(): ?\DateTimeInterface
    {
        return $this->lockedUntil;
    }
    public function setLockedUntil(?\DateTimeInterface $lockedUntil): self
    {
        $this->lockedUntil = $lockedUntil;
        return $this;
    }
    public function getLastAccess(): ?\DateTimeInterface
    {
        return $this->lastAccess;
    }
    public function setLastAccess(?\DateTimeInterface $lastAccess): self
    {
        $this->lastAccess = $lastAccess;
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
}
