<?php
declare(strict_types=1);
namespace App\User\Infrastructure\InputAdapters;

use App\User\Infrastructure\InputPorts\LoginUserInputPort;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class LoginUserController {

    private LoginUserInputPort $loginInputPort;
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(LoginUserInputPort $loginInputPort, JWTTokenManagerInterface $jwtManager)
    {
        $this->loginInputPort = $loginInputPort;
        $this->jwtManager = $jwtManager;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $mail = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$mail || !$password) {
            return new JsonResponse(['error' => 'Email and password are required'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $user = $this->loginInputPort->login($mail, $password);
        if (!$user) {
            return new JsonResponse(['error' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = $this->jwtManager->create($user);
        return new JsonResponse(['token' => $token]);
    }
}
