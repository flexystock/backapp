<?php

declare(strict_types=1);

namespace App\Entity\Main;

use App\User\Application\DTO\Auth\CreateUserRequest;
use App\User\Repository\UserRepository;
use App\Entity\Main\Role;
use App\Entity\Main\Profile;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'email_UNIQUE', columns: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true)]
    private string $uuid_user;

    #[ORM\Column(type: 'string', length: 50)]
    private string $name;

    #[ORM\Column(type: 'string', length: 50)]
    private string $surnames;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?int $phone = 60;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    private string $password;

    #[ORM\Column(type: 'boolean')]
    private bool $is_root = false;

    #[ORM\Column(type: 'boolean')]
    private bool $is_ghost = false;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $minutes_session_expiration = 60;

    #[ORM\Column(type: 'boolean')]
    private bool $active = true;

    #[ORM\Column(type: 'smallint')]
    private int $failed_attempts = 0;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $locked_until = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $last_access = null;

    #[ORM\Column(type: 'string', length: 36)]
    private string $uuid_user_creation;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $date_hour_creation;

    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $uuid_user_modification = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $date_hour_modification = null;

    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $document_type = null;

    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $document_number = null;

    #[ORM\Column(type: 'string', length: 36, nullable: false)]
    private ?string $timezone;

    #[ORM\Column(type: 'string', length: 36, nullable: false)]
    private ?string $language;

    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $preferred_contact_method = null;

    #[ORM\Column(type: 'boolean')]
    private bool $two_factor_enabled = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $security_question = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $security_answer = null;

    #[ORM\Column(name: 'verification_token', type: 'string', length: 255, nullable: true)]
    private ?string $verification_token = null;

    #[ORM\Column(name: 'verification_token_expires_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $verification_token_expires_at = null;

    #[ORM\Column(name: 'is_verified', type: 'boolean')]
    private bool $is_verified = false;

    private ?string $selectedClientUuid = null;

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users', fetch: 'EAGER')]
    #[ORM\JoinTable(name: 'user_role',
        joinColumns: [new ORM\JoinColumn(name: 'uuid_user', referencedColumnName: 'uuid_user')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id')]
    )]
    private Collection $roles;

    #[ORM\ManyToMany(targetEntity: Client::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'user_client',
        joinColumns: [new ORM\JoinColumn(name: 'uuid_user', referencedColumnName: 'uuid_user')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'uuid_client', referencedColumnName: 'uuid_client')]
    )]
    private Collection $clients;

    #[ORM\ManyToMany(targetEntity: BusinessGroup::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'user_business_group')]
    private Collection $businessGroups;

    #[ORM\ManyToOne(targetEntity: Profile::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'profile_id', referencedColumnName: 'id')]
    private ?Profile $profile = null;

    public function __construct()
    {
        $this->uuid_user = Uuid::v4()->toRfc4122();
        $this->roles = new ArrayCollection();
        $this->clients = new ArrayCollection();
        $this->businessGroups = new ArrayCollection();
    }

    /**
     * Get user UUID.
     *
     * @return string|null UUID identifier
     */
    public function getUuid(): ?string
    {
        return $this->uuid_user;
    }

    /**
     * Set user UUID.
     *
     * @param string $uuid UUID identifier
     *
     * @return self
     */
    public function setUuid(string $uuid): self
    {
        $this->uuid_user = $uuid;

        return $this;
    }

    /**
     * Get user name.
     *
     * @return string name of the user
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set user name.
     *
     * @param string $name user full name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get surnames.
     *
     * @return string surnames of the user
     */
    public function getSurnames(): string
    {
        return $this->surnames;
    }

    /**
     * Set surnames.
     *
     * @param string $surnames user surnames
     *
     * @return self
     */
    public function setSurnames(string $surnames): self
    {
        $this->surnames = $surnames;

        return $this;
    }

    /**
     * Get phone number.
     *
     * @return int phone number
     */
    public function getPhone(): int
    {
        return $this->phone;
    }

    /**
     * Set phone number.
     *
     * @param int $phone contact phone
     *
     * @return self
     */
    public function setPhone(int $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get email address.
     *
     * @return string email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set email address.
     *
     * @param string $email email address
     *
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        // Roles asociados vía la tabla user_role
        $roles = [];

        // Garantizamos que la colección esté inicializada por si Doctrine la cargó de forma perezosa
        if ($this->roles instanceof PersistentCollection && !$this->roles->isInitialized()) {
            $this->roles->initialize();
        }

        foreach ($this->roles as $role) {
            $roles[] = 'ROLE_' . strtoupper($role->getName());
        }

        if ($this->isRoot()) {
            $roles[] = 'ROLE_ROOT';
        }

        // Si no tiene ningún rol asociado, se asigna el rol por defecto
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function getRoleEntities(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
            $role->addUser($this);
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->roles->removeElement($role)) {
            $role->removeUser($this);
        }

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function hasPermission(string $permissionName): bool
    {
        if ($this->isRoot()) {
            return true;
        }

        if ($this->profile === null) {
            return false;
        }

        foreach ($this->profile->getProfilePermissions() as $pp) {
            if ($pp->getPermission()->getName() === $permissionName) {
                return true;
            }
        }

        return false;
    }

    public function eraseCredentials(): void
    {
        // Si almacenas datos sensibles temporales, límpialos aquí.
    }

    public function isRoot(): bool
    {
        return $this->is_root;
    }

    public function setIsRoot(bool $isRoot): self
    {
        $this->is_root = $isRoot;

        return $this;
    }

    public function isGhost(): bool
    {
        return $this->is_ghost;
    }

    public function setIsGhost(bool $isGhost): self
    {
        $this->is_ghost = $isGhost;

        return $this;
    }

    public function getMinutesSessionExpiration(): ?int
    {
        return $this->minutes_session_expiration;
    }

    public function setMinutesSessionExpiration(?int $minutesSessionExpiration): self
    {
        $this->minutes_session_expiration = $minutesSessionExpiration;

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
        return $this->failed_attempts;
    }

    public function setFailedAttempts(int $failedAttempts): self
    {
        $this->failed_attempts = $failedAttempts;

        return $this;
    }

    public function getLockedUntil(): ?\DateTimeInterface
    {
        return $this->locked_until;
    }

    public function setLockedUntil(?\DateTimeInterface $lockedUntil): self
    {
        $this->locked_until = $lockedUntil;

        return $this;
    }

    public function getLastAccess(): ?\DateTimeInterface
    {
        return $this->last_access;
    }

    public function setLastAccess(?\DateTimeInterface $lastAccess): self
    {
        $this->last_access = $lastAccess;

        return $this;
    }

    public function getUuidUserCreation(): string
    {
        return $this->uuid_user_creation;
    }

    public function setUuidUserCreation(string $uuidUserCreation): self
    {
        $this->uuid_user_creation = $uuidUserCreation;

        return $this;
    }

    public function getDatehourCreation(): \DateTimeInterface
    {
        return $this->date_hour_creation;
    }

    public function setDatehourCreation(\DateTimeInterface $datehourCreation): self
    {
        $this->date_hour_creation = $datehourCreation;

        return $this;
    }

    public function getUuidUserModification(): ?string
    {
        return $this->uuid_user_modification;
    }

    public function setUuidUserModification(?string $uuidUserModification): self
    {
        $this->uuid_user_modification = $uuidUserModification;

        return $this;
    }

    public function getDatehourModification(): ?\DateTimeInterface
    {
        return $this->date_hour_modification;
    }

    public function setDatehourModification(?\DateTimeInterface $datehourModification): self
    {
        $this->date_hour_modification = $datehourModification;

        return $this;
    }

    public function getDocumentType(): ?string
    {
        return $this->document_type;
    }

    public function setDocumentType(string $document_type): self
    {
        $this->document_type = $document_type;

        return $this;
    }

    public function getDocumentNumber(): string
    {
        return $this->document_number;
    }

    public function setDocumentNumber(string $document_number): self
    {
        $this->document_number = $document_number;

        return $this;
    }

    public function getTimeZone(): string
    {
        return $this->timezone;
    }

    public function setTimeZone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getPreferredContactMethod(): string
    {
        return $this->preferred_contact_method;
    }

    public function setPreferredContactMethod(string $preferred_contact_method): self
    {
        $this->preferred_contact_method = $preferred_contact_method;

        return $this;
    }

    public function getTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled;
    }

    public function setTwoFactorEnabled(bool $two_factor_enabled): self
    {
        $this->two_factor_enabled = $two_factor_enabled;

        return $this;
    }

    public function getSecurityQuestion(): string
    {
        return $this->security_question;
    }

    public function setSecurityQuestion(string $security_question): self
    {
        $this->security_question = $security_question;

        return $this;
    }

    public function getSecurityAnswer(): string
    {
        return $this->security_answer;
    }

    public function setSecurityAnswer(string $security_answer): self
    {
        $this->security_answer = $security_answer;

        return $this;
    }

    public function getVerificationToken(): ?string
    {
        return $this->verification_token;
    }

    public function setVerificationToken(?string $verificationToken): self
    {
        $this->verification_token = $verificationToken;

        return $this;
    }

    public function getVerificationTokenExpiresAt(): ?\DateTimeInterface
    {
        return $this->verification_token_expires_at;
    }

    public function setVerificationTokenExpiresAt(?\DateTimeInterface $expiresAt): self
    {
        $this->verification_token_expires_at = $expiresAt;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->is_verified = $isVerified;

        return $this;
    }

    // En la entidad User
    public static function fromCreateUserRequest(CreateUserRequest $request, UserPasswordHasherInterface $passwordHasher): self
    {
        $user = new self();
        $user->setEmail($request->getEmail());
        $user->setName($request->getName());
        $user->setSurnames($request->getSurnames());
        $user->setPassword(
            $passwordHasher->hashPassword($user, $request->getPass())
        );
        $user->setPhone((int) $request->getPhone());
        $user->setDocumentType($request->getDocumentType());
        $user->setDocumentNumber($request->getDocumentNumber());
        $user->setTimeZone($request->getTimezone());
        $user->setLanguage($request->getLanguage());
        $user->setPreferredContactMethod($request->getPreferredContactMethod());
        $user->setTwoFactorEnabled($request->isTwoFactorEnabled());
        $user->setUuidUserCreation($user->getUuid());
        $user->setDatehourCreation(new \DateTime());

        return $user;
    }

    // Funcion para agregar un cliente a la colección
    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
            $client->addUser($this); // Si deseas mantener la sincronización bidireccional
        }

        return $this;
    }

    // Funcion para eliminar un cliente de la colección
    public function removeClient(Client $client): self
    {
        if ($this->clients->removeElement($client)) {
            $client->removeUser($this);
        }

        return $this;
    }

    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function getSelectedClientUuid(): ?string
    {
        return $this->selectedClientUuid;
    }

    public function setSelectedClientUuid(?string $selectedClientUuid): void
    {
        $this->selectedClientUuid = $selectedClientUuid;
    }
}
