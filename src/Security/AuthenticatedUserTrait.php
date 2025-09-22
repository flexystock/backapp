<?php

namespace App\Security;

use App\Entity\Main\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait AuthenticatedUserTrait
{
    /**
     * Get authenticated user as User entity.
     *
     * @throws \LogicException if user is not authenticated or not an instance of User
     */
    protected function getAuthenticatedUser(): User
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new \LogicException('User must be authenticated and be an instance of App\Entity\Main\User');
        }

        return $user;
    }

    /**
     * Get authenticated user or return JSON error response.
     *
     * @return User|JsonResponse Returns User instance or JsonResponse with error
     */
    protected function getAuthenticatedUserOrError(string $errorMessage = 'USER_NOT_AUTHENTICATED'): User|JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['message' => $errorMessage], Response::HTTP_UNAUTHORIZED);
        }

        return $user;
    }

    /**
     * Check if user is authenticated and is instance of User entity.
     */
    protected function isUserAuthenticated(): bool
    {
        $user = $this->getUser();

        return $user instanceof User;
    }
}
