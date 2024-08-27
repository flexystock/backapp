<?php
declare(strict_types=1);
namespace App\User\Infrastructure\InputAdapters;

use App\User\Infrastructure\InputPorts\LoginUserInputPort;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use OpenApi\Attributes as OA;
use App\Entity\Main\User;

class LoginUserController {

    private LoginUserInputPort $loginInputPort;
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(LoginUserInputPort $loginInputPort, JWTTokenManagerInterface $jwtManager)
    {
        $this->loginInputPort = $loginInputPort;
        $this->jwtManager = $jwtManager;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    #[OA\Post(
        path: '/api/login',
        summary: 'Login User',
        tags: ['User'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'email', type: 'string'),
                    new OA\Property(property: 'password', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'User login successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'status', type: 'string'),
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'token', type: 'string'),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input'
            )
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $mail = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$this->isValidLoginRequest($mail, $password)) {
            return $this->jsonResponse(['error' => 'Invalid email or password'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $this->loginInputPort->login($mail, $password, $request->getClientIp());

        if (!$user || $this->isAccountLocked($user)) {
            return $this->jsonResponse(['error' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        try {
            $token = $this->jwtManager->create($user);
            return $this->jsonResponse(['token' => $token]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    private function isValidLoginRequest(?string $mail, ?string $password): bool
    {
        return $mail && filter_var($mail, FILTER_VALIDATE_EMAIL) && $password;
    }

    private function isAccountLocked(User $user): bool
    {
        $criticalAttempts = [4, 7, 10, 13];
        if (in_array($user->getFailedAttempts(), $criticalAttempts, true)) {
            $this->loginInputPort->handleFailedLogin($user);
            return true;
        }
        return false;
    }

    private function jsonResponse(array $data, int $status = 200): JsonResponse
    {
        $response = new JsonResponse($data, $status);
        $response->headers->set('Cache-Control', 'no-cache, private');
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        return $response;
    }
}
