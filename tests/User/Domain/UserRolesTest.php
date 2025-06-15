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

        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_ROOT', $roles);
        $this->assertContains('ROLE_USER', $roles);
    }
}
