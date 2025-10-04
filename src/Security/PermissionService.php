<?php

namespace App\Security;

use App\Entity\Main\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PermissionService
{
    private TokenStorageInterface $tokenStorage;
    private AuthorizationCheckerInterface $authorizationChecker;

    // Role hierarchy levels (higher number = more permissions)
    private const ROLE_LEVELS = [
        'ROLE_ROOT' => 1,
        'ROLE_SUPERADMIN' => 2,
        'ROLE_ADMIN' => 3,
        'ROLE_MANAGER' => 4,
        'ROLE_USER' => 5,
    ];

    // Permission definitions per role
    private const ROLE_PERMISSIONS = [
        'ROLE_ROOT' => ['*'], // Full access
        'ROLE_SUPERADMIN' => ['*'], // Full access
        'ROLE_ADMIN' => [
            'alarm.view', 
            'alarm.create', 
            'alarm.update', 
            'alarm.delete',
            'analytics.view',
            'product.create', 
            'product.dashboard',
            'product.delete',
            'product.view', 
            'product.update', 
            'scale.view', 
            'scale.assign',
            'scale.create', 
            'scale.dashboard',
            'scale.delete',
            'scale.unassign',
            'scale.update', 
            'subscription.create', 
            'subscription.delete',
            'subscription.view', 
            'subscription.update', 
            'users.dashboard',
            'user.create', 
            'user.delete',
            'user.update', 
            'user.view',
            'client.view'
        ],
        'ROLE_MANAGER' => [
            'alarm.update',
            'alarm.view', 
            'analytics.view',
            'product.create', 
            'product.dashboard',
            'product.view', 
            'product.update',
            'scale.dashboard', 
            'scale.update',
            'scale.view', 
            'user.update',
            'user.view', 
        ],
        'ROLE_USER' => [
            'product.dashboard',
            'product.view',
            'scale.view',
            'scale.dashboard'
        ],
    ];

    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Check if current user has permission for an action.
     */
    public function hasPermission(string $permission): bool
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return false;
        }

        return $this->userHasPermission($user, $permission);
    }

    /**
     * Check if user has permission for an action.
     */
    public function userHasPermission(User $user, string $permission): bool
    {
        // Get user's highest role
        $userRole = $this->getUserHighestRole($user);

        if (!$userRole) {
            return false;
        }

        // Check if role has full access
        if (in_array('*', self::ROLE_PERMISSIONS[$userRole] ?? [])) {
            return true;
        }

        // Check specific permission
        $allowedPermissions = self::ROLE_PERMISSIONS[$userRole] ?? [];

        return in_array($permission, $allowedPermissions);
    }

    /**
     * Check if current user has any of the specified roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->authorizationChecker->isGranted($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if current user has minimum role level.
     */
    public function hasMinimumRoleLevel(string $minimumRole): bool
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return false;
        }

        $userRole = $this->getUserHighestRole($user);
        if (!$userRole) {
            return false;
        }

        $userLevel = self::ROLE_LEVELS[$userRole] ?? 999;
        $minimumLevel = self::ROLE_LEVELS[$minimumRole] ?? 999;

        return $userLevel <= $minimumLevel;
    }

    /**
     * Get current user's highest role.
     */
    private function getUserHighestRole(User $user): ?string
    {
        $userRoles = $user->getRoles(); // Assuming this returns role names
        $highestLevel = 999; // Start with highest number (lowest privilege)
        $highestRole = null;

        foreach ($userRoles as $role) {
            $level = self::ROLE_LEVELS[$role] ?? 999;
            if ($level < $highestLevel) { // Lower number = higher privilege
                $highestLevel = $level;
                $highestRole = $role;
            }
        }

        return $highestRole;
    }

    /**
     * Get current authenticated user.
     */
    private function getCurrentUser(): ?User
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return null;
        }

        $user = $token->getUser();

        return $user instanceof User ? $user : null;
    }

    /**
     * Get all permissions for a role.
     */
    public function getRolePermissions(string $role): array
    {
        return self::ROLE_PERMISSIONS[$role] ?? [];
    }

    /**
     * Check if role exists.
     */
    public function roleExists(string $role): bool
    {
        return isset(self::ROLE_LEVELS[$role]);
    }
}
