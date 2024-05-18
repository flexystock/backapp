<?php
declare(strict_types=1);

// src/Main/Entity/User.php
namespace App\Main\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'user')]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'string')]
    private $password;

    // Aquí puedes agregar más campos según tus necesidades

    // Implementación de los métodos de UserInterface

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

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

    public function getUsername(): string
    {
        return $this->email; // O cualquier otro campo que quieras usar como nombre de usuario
    }

    public function getRoles(): array
    {
        // Por ejemplo, puedes retornar un array con los roles del usuario
        return ['ROLE_USER'];
    }

    public function getSalt(): ?string
    {
        // No necesitas un salt si estás usando bcrypt o sodium
        return null;
    }

    public function eraseCredentials()
    {
        // Si tienes datos sensibles almacenados en el usuario, límpialos aquí
    }

    public function getUserIdentifier(): string
    {
        // TODO: Implement getUserIdentifier() method.
    }
}
