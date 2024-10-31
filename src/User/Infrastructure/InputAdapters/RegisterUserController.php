<?php
declare(strict_types=1);
namespace App\User\Infrastructure\InputAdapters;

use App\User\Application\RegisterUserUseCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use App\User\Application\DTO\CreateUserRequest;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Serializer\SerializerInterface;

class RegisterUserController
{
    private RegisterUserUseCase $registerUseCase;
    private ValidatorInterface $validator;
    private SerializerInterface $serializer;

    public function __construct(RegisterUserUseCase $registerUseCase, ValidatorInterface $validator,
                                SerializerInterface $serializer)
    {
        $this->registerUseCase = $registerUseCase;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    #[Route('/api/user_register', name: 'user_register', methods: ['POST'])]
    #[OA\Post(
        path: '/api/user_register',
        summary: 'Registrar un nuevo usuario y cliente',
        tags: ['User'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['full_name', 'email', 'password'],
                properties: [
                    new OA\Property(property: 'full_name', type: 'string'),
                    new OA\Property(property: 'email', type: 'string'),
                    new OA\Property(property: 'password', type: 'string'),
                    new OA\Property(property: 'phone_number', type: 'integer'),
                    new OA\Property(property: 'document_type', type: 'string'),
                    new OA\Property(property: 'document_number', type: 'string'),
                    new OA\Property(property: 'timezone', type: 'string'),
                    new OA\Property(property: 'language', type: 'string'),
                    new OA\Property(property: 'preferred_contact_method', type: 'string'),
                    new OA\Property(property: 'two_factor_enabled', type: 'boolean'),
                    new OA\Property(property: 'security_question', type: 'string'),
                    new OA\Property(property: 'security_answer', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Usuario registrado exitosamente',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer'),
                                new OA\Property(property: 'email', type: 'string'),
                                new OA\Property(property: 'full_name', type: 'string'),
                                // Añade otros campos que retornas en la respuesta
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Entrada inválida',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'error', type: 'string')
                    ]
                )
            )
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
                    'errors' => $errorMessages
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
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            // Otros errores
            return new JsonResponse([
                'message' => 'Ocurrió un error al registrar el usuario.',
                'error' => $e->getMessage()
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
     * @param ConstraintViolationListInterface $errors Lista de violaciones de restricciones obtenida tras la validación.
     *
     * @return array Arreglo asociativo con los errores formateados. La estructura es:
     *               [
     *                   'nombreDelCampo' => 'Mensaje de error',
     *                   // ...
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