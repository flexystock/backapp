<?php

namespace App\Tests\User\Domain;

use App\Entity\Main\User;
use App\Entity\Main\Role;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class UserRolesTest extends TestCase
{
    public function testGetRolesIncludesDatabaseRolesAndRoot(): void
    {
        $user = new User();
        $user->setIsRoot(true);

        $role = new Role();
        $role->setName('admin');

        $collection = new ArrayCollection([$role]);
        $property = new \ReflectionProperty(User::class, 'roles');
        $property->setAccessible(true);
        $property->setValue($user, $collection);

        $roles = $user->getRoles();

        // Verificar que contiene el rol de la base de datos
        $this->assertContains('ROLE_ADMIN', $roles);

        // Verificar que contiene el rol de root
        $this->assertContains('ROLE_ROOT', $roles);

        // ROLE_USER solo se añade si no hay otros roles
        // Por eso NO se verifica aquí
    }

    public function testGetRolesIncludesDefaultRoleWhenNoRolesAssigned(): void
    {
        $user = new User();
        // No asignar roles ni root

        $roles = $user->getRoles();

        // Cuando no hay roles, debe devolver ROLE_USER por defecto
        $this->assertContains('ROLE_USER', $roles);
        $this->assertCount(1, $roles);
    }

    public function testGetRolesDoesNotDuplicateRoles(): void
    {
        $user = new User();
        $user->setIsRoot(true);

        $role1 = new Role();
        $role1->setName('admin');

        $role2 = new Role();
        $role2->setName('admin'); // Mismo rol dos veces

        $collection = new ArrayCollection([$role1, $role2]);
        $property = new \ReflectionProperty(User::class, 'roles');
        $property->setAccessible(true);
        $property->setValue($user, $collection);

        $roles = $user->getRoles();

        // array_unique debe evitar duplicados
        $this->assertEquals(count($roles), count(array_unique($roles)));
        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_ROOT', $roles);
    }
}