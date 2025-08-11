<?php

namespace App\Security;

use App\Entity\Main\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PermissionVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return is_string($attribute) && str_starts_with($attribute, 'PERMISSION_');
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        $permission = substr($attribute, strlen('PERMISSION_'));

        return $user->hasPermission($permission);
    }
}
