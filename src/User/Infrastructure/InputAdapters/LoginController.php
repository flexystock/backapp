<?php
declare(strict_types=1);
namespace App\User\Infrastructure\InputAdapters;

use App\User\Infrastructure\InputPorts\LoginInputPort;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\User\Infrastructure\OutputPorts\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginController {

    private $loginInputPort;

    public function __construct(LoginInputPort $loginInputPort)
    {
        $this->loginInputPort = $loginInputPort;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Email and password are required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $this->loginInputPort->login($email, $password);
        if (!$user) {
            return new JsonResponse(['error' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Aquí es donde integrarías la generación de JWT
        return new JsonResponse(['message' => 'Login successful', 'user' => $user->getEmail()]);
        //return new JsonResponse(['message' => 'Los entornos esta bien configurados']);
    }
}
