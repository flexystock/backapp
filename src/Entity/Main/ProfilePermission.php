<?php

declare(strict_types=1);

namespace App\Entity\Main;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'profile_permission')]
class ProfilePermission
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Profile::class)]
    #[ORM\JoinColumn(name: 'profile_id', referencedColumnName: 'id')]
    private Profile $profile;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Permission::class, inversedBy: 'profilePermissions')]
    #[ORM\JoinColumn(name: 'permission_id', referencedColumnName: 'id')]
    private Permission $permission;

    public function getProfile(): Profile
    {
        return $this->profile;
    }

    public function setProfile(Profile $profile): void
    {
        $this->profile = $profile;
    }

    public function getPermission(): Permission
    {
        return $this->permission;
    }

    public function setPermission(Permission $permission): void
    {
        $this->permission = $permission;
    }
}
