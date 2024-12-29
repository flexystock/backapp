<?php

declare(strict_types=1);

namespace App\Client\Infrastructure\InputAdapters;

use App\Client\Application\DTO\RegisterClientRequest;
use App\Client\Application\InputPorts\RegisterClientInputPort;
use App\Client\Application\UseCases\RegisterClientUseCase;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterClientController
{
    private RegisterClientInputPort $registerInputPort;
    private RegisterClientUseCase $registerClientUseCase;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(RegisterClientInputPort $registerInputPort,
        RegisterClientUseCase $registerClientUseCase,
        SerializerInterface $serializer,
        ValidatorInterface $validator)
    {
        $this->registerInputPort = $registerInputPort;
        $this->registerClientUseCase = $registerClientUseCase;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    #[Route('/api/client_register', name: 'api_register', methods: ['POST'])]
    #[OA\Post(
        path: '/api/client_register',
        summary: 'Create a new Client',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'nifCif', type: 'string'),
                    new OA\Property(property: 'foundationDate', type: 'string'),
                    new OA\Property(property: 'physicalAddress', type: 'string'),
                    new OA\Property(property: 'city', type: 'string'),
                    new OA\Property(property: 'country', type: 'string'),
                    new OA\Property(property: 'postalCode', type: 'integer'),
                    new OA\Property(property: 'companyPhone', type: 'string'),
                    new OA\Property(property: 'companyEmail', type: 'string'),
                    new OA\Property(property: 'numberOfEmployees', type: 'integer'),
                    new OA\Property(property: 'industrySector', type: 'string'),
                    new OA\Property(property: 'averageInventoryVolume', type: 'integer'),
                    new OA\Property(property: 'currency', type: 'string'),
                    new OA\Property(property: 'numberWarehouses', type: 'integer'),
                    new OA\Property(property: 'annualSalesVolume', type: 'integer'),
                ],
                type: 'object'
            )
        ),
        tags: ['Client'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Clientsuccessfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string'),
                        new OA\Property(
                            property: 'client',
                            properties: [
                                new OA\Property(property: 'name', type: 'string'),
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
    public function __invoke(Request $request): JsonResponse
    {
        try {
            // Deserializar el contenido JSON en una instancia del DTO
            $clientRequest = $this->serializer->deserialize(
                $request->getContent(),
                RegisterClientRequest::class,
                'json'
            );

            $errors = $this->validator->validate($clientRequest);

            if (count($errors) > 0) {
                $errorMessages = $this->formatValidationErrors($errors);

                return new JsonResponse([
                    'message' => 'Datos inválidos',
                    'errors' => $errorMessages,
                ], Response::HTTP_BAD_REQUEST);
            }

            $this->registerInputPort->register($clientRequest);

            return new JsonResponse(['success' => true], 201);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
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
