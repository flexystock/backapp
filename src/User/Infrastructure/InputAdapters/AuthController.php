<?php

declare(strict_types=1);

namespace App\User\Infrastructure\InputAdapters;

use App\Entity\Main\User;
use App\User\Application\DTO\Auth\CreateUserRequest;
use App\User\Application\InputPorts\Auth\LoginUserInputPort;
use App\User\Application\InputPorts\Auth\SelectClientInputPort;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use App\User\Application\UseCases\Auth\RegisterUserUseCase;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController
{
    private LoginUserInputPort $loginInputPort;
    private JWTTokenManagerInterface $jwtManager;
    private UserRepositoryInterface $userRepository;
    private RegisterUserUseCase $registerUseCase;
    private ValidatorInterface $validator;
    private SerializerInterface $serializer;
    private SelectClientInputPort $selectClientInputPort;
    private TokenStorageInterface $tokenStorage;

    public function __construct(LoginUserInputPort $loginInputPort,
        UserRepositoryInterface $userRepository,
        JWTTokenManagerInterface $jwtManager,
        RegisterUserUseCase $registerUseCase,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        SelectClientInputPort $selectClientInputPort,
        TokenStorageInterface $tokenStorage)
    {
        $this->loginInputPort = $loginInputPort;
        $this->jwtManager = $jwtManager;
        $this->userRepository = $userRepository;
        $this->registerUseCase = $registerUseCase;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->selectClientInputPort = $selectClientInputPort;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    #[OA\Post(
        path: '/api/login',
        summary: 'Login User',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'email', type: 'string'),
                    new OA\Property(property: 'password', type: 'string'),
                ],
                type: 'object'
            )
        ),
        tags: ['User'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User login successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string'),
                        new OA\Property(
                            property: 'user',
                            properties: [
                                new OA\Property(property: 'token', type: 'string'),
                            ],
                            type: 'object'
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input'
            ),
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $mail = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$this->isValidLoginRequest($mail, $password)) {
            return $this->jsonResponse(['error' => 'Invalid email or password'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->loginInputPort->login($mail, $password, $request->getClientIp());

        if (!$user) {
            // Usuario no existe o credenciales incorrectas
            $user = $this->userRepository->findByEmail($mail);
            if ($user) {
                // El usuario existe, manejar intentos fallidos
                $lockMessage = $this->loginInputPort->handleFailedLogin($user);
                if ($lockMessage) {
                    return $this->jsonResponse(['error' => $lockMessage], Response::HTTP_UNAUTHORIZED);
                }
            }

            // No revelar si el usuario no existe
            return $this->jsonResponse(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }
        $verified = $user->isVerified();
        if (!$verified) {
            return $this->jsonResponse(['error' => 'Usuario NO verificado'], Response::HTTP_UNAUTHORIZED);
        }
        // Verificar si la cuenta está bloqueada
        if ($user->getLockedUntil() && $user->getLockedUntil() > new \DateTimeImmutable()) {
            $lockedUntil = $user->getLockedUntil()->format('Y-m-d H:i:s');

            return $this->jsonResponse(['error' => "Su cuenta está bloqueada hasta: $lockedUntil."], Response::HTTP_UNAUTHORIZED);
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

    #[Route('/api/user_register', name: 'user_register', methods: ['POST'])]
    #[OA\Post(
        path: '/api/user_register',
        summary: 'Registrar un nuevo usuario y cliente',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['full_name', 'email', 'password', 'timezone', 'language'],
                properties: [
                    new OA\Property(property: 'full_name', type: 'string'),
                    new OA\Property(property: 'surnames', type: 'string'),
                    new OA\Property(property: 'email', type: 'string'),
                    new OA\Property(property: 'password', type: 'string'),
                    new OA\Property(property: 'phone_number', type: 'string'),
                    new OA\Property(property: 'document_type', type: 'string'),
                    new OA\Property(property: 'document_number', type: 'string'),
                    new OA\Property(property: 'timezone', type: 'datetime'),
                    new OA\Property(property: 'language', type: 'string'),
                    new OA\Property(property: 'preferred_contact_method', type: 'string'),
                    new OA\Property(property: 'two_factor_enabled', type: 'boolean'),

                ],
                type: 'object'
            )
        ),
        tags: ['User'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Usuario registrado exitosamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(
                            property: 'user',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer'),
                                new OA\Property(property: 'email', type: 'string'),
                                new OA\Property(property: 'full_name', type: 'string'),
                            ],
                            type: 'object'
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Entrada inválida',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'error', type: 'string'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            // Deserializar el contenido JSON en una instancia del DTO
            $userRequest = $this->serializer->deserialize(
                $request->getContent(),
                CreateUserRequest::class,
                'json'
            );

            // Validar el DTO
            $errors = $this->validator->validate($userRequest);

            if (count($errors) > 0) {
                $errorMessages = $this->formatValidationErrors($errors);

                return new JsonResponse([
                    'message' => 'Datos inválidos',
                    'errors' => $errorMessages,
                ], Response::HTTP_BAD_REQUEST);
            }

            $user = $this->registerUseCase->register($userRequest);

            $responseContent = [
                'message' => 'Usuario registrado exitosamente',
                'user' => [
                    'id' => $user->getUuid(),
                    'email' => $user->getEmail(),
                ],
            ];

            return new JsonResponse($responseContent, Response::HTTP_CREATED);
        } catch (\Symfony\Component\Serializer\Exception\NotEncodableValueException $e) {
            // Error al deserializar el JSON (formato inválido)
            return new JsonResponse([
                'message' => 'Formato JSON inválido.',
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            // Otros errores
            return new JsonResponse([
                'message' => 'Ocurrió un error al registrar el usuario.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Formatea una lista de errores de validación en un array asociativo.
     *
     * Este métoodo toma una lista de violaciones de restricciones (errores de validación)
     * y las convierte en un array donde cada clave es el nombre del campo que contiene
     * el error y cada valor es el mensaje de error correspondiente. Esto facilita la
     * preparación de respuestas JSON claras y estructuradas para informar al cliente
     * sobre los errores de validación ocurridos.
     *
     * @param ConstraintViolationListInterface $errors lista de violaciones de restricciones obtenida tras la validación
     *
     * @return array Arreglo asociativo con los errores formateados. La estructura es:
     *               [
     *               'nombreDelCampo' => 'Mensaje de error',
     *               // ...
     *               ]
     */
    private function formatValidationErrors(ConstraintViolationListInterface $errors): array
    {
        $errorMessages = [];

        foreach ($errors as $error) {
            $field = $error->getPropertyPath();
            $message = $error->getMessage();

            // Agregar el error al arreglo
            $errorMessages[$field] = $message;
        }

        return $errorMessages;
    }
}
