<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait PermissionControllerTrait
{
    private PermissionService $permissionService;

    /**
     * Check if user has permission and throw exception if not.
     */
    protected function requirePermission(string $permission, string $message = 'No tienes permisos'): void
    {
        if (!$this->permissionService->hasPermission($permission)) {
            throw $this->createAccessDeniedException($message);
        }
    }

    /**
     * Check if user has any of the specified permissions.
     */
    protected function requireAnyPermission(array $permissions, string $message = 'No tienes permisos'): void
    {
        foreach ($permissions as $permission) {
            if ($this->permissionService->hasPermission($permission)) {
                return;
            }
        }
        throw $this->createAccessDeniedException($message);
    }

    /**
     * Check if user has minimum role level.
     */
    protected function requireMinimumRole(string $role, string $message = 'No tienes permisos'): void
    {
        if (!$this->permissionService->hasMinimumRoleLevel($role)) {
            throw $this->createAccessDeniedException($message);
        }
    }

    /**
     * Check permission and return JSON response if denied.
     */
    protected function checkPermissionJson(string $permission, string $message = 'No tienes permisos'): ?JsonResponse
    {
        if (!$this->permissionService->hasPermission($permission)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $message,
            ], Response::HTTP_FORBIDDEN);
        }

        return null;
    }

    /**
     * Check any permission and return JSON response if denied.
     */
    protected function checkAnyPermissionJson(array $permissions, string $message = 'No tienes permisos'): ?JsonResponse
    {
        foreach ($permissions as $permission) {
            if ($this->permissionService->hasPermission($permission)) {
                return null;
            }
        }

        return new JsonResponse([
            'status' => 'error',
            'message' => $message,
        ], Response::HTTP_FORBIDDEN);
    }

    /**
     * Legacy method for backward compatibility.
     */
    protected function checkAdminAccess(): ?JsonResponse
    {
        return $this->checkAnyPermissionJson([
            'ROLE_ROOT', 'ROLE_SUPERADMIN', 'ROLE_ADMIN',
        ], 'No tienes permisos de administrador');
    }
}
