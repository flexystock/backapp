<?php

namespace App\Client\Infrastructure\InputAdapters;

use App\Client\Application\DTO\GetInfoClientRequest;
use App\Client\Application\DTO\GetInfoClientResponse;
use App\Client\Application\InputPorts\GetInfoClientInputPort;
use App\Client\Application\UseCases\GetInfoClientUseCase;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class GetInfoClientController extends AbstractController
{
    use PermissionControllerTrait;

    private GetInfoClientInputPort $getInfoClientInputPort;
    private GetInfoClientUseCase $getInfoClientUseCase;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private LoggerInterface $logger;

    public function __construct (GetInfoClientInputPort $getInfoClientInputPort,
                                 GetInfoClientUseCase   $getInfoClientUseCase,
                                 SerializerInterface    $serializer,
                                 ValidatorInterface     $validator,
                                 LoggerInterface $logger,
                                 PermissionService $permissionService)
    {
        $this->getInfoClientInputPort = $getInfoClientInputPort;
        $this->getInfoClientUseCase = $getInfoClientUseCase;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/client_info', name: 'api_client_info', methods: ['POST'])]
    #[RequiresPermission('client.view')]
    #[OA\Post(
        path: '/api/client_info',
        summary: 'Get information of a Client by UUID',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'uuid', type: 'string', example: '492e5a45-9ad9-4876-87f7-0788d842f17d'),
                ],
                type: 'object'
            )
        ),
        tags: ['Client'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Client information retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'client', type: 'object', example: [
                            'uuidClient' => '492e5a45-9ad9-4876-87f7-0788d842f17d',
                            'name' => 'Restaurante Pepe',
                            'nifCif' => 'B12345678',
                            'foundationDate' => '2000-01-01',
                            'fiscalAddress' => 'Calle Falsa 123',
                            'physicalAddress' => 'Calle Falsa 123',
                            'city' => 'Madrid',
                            'country' => 'Spain',
                            'postalCode' => 28080,
                            'companyPhone' => '+34123456789',
                            'companyEmail' => 'kjas@gmail.com',
                            'numberOfEmployees' => 50,
                            'industrySector' => 'Hospitality',
                            'averageInventoryVolume' => 1000,
                            'currency' => 'EUR',
                            'numberWarehouses' => 2,
                            'annualSalesVolume' => 500000,
                        ]),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input data',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Invalid input data: ...'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Client not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Client not found'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function getInfoClient (Request $request): JsonResponse
    {
        $permissionCheck = $this->checkPermissionJson('client.view', 'No tienes permisos para crear un producto');
        if ($permissionCheck) {
            return $permissionCheck;
        }

        $data = $request->getContent();
        $getInfoClientRequest = $this->serializer->deserialize($data, GetInfoClientRequest::class, 'json');

        $uuidClient = $getInfoClientRequest->getUuidClient();
        $errors = $this->validator->validate($getInfoClientRequest);
        if (count($errors) > 0) {
            $errorMessages = $this->getErrorMessages($errors);
            return new JsonResponse(['error' => 'Invalid input data: ' . implode(', ', $errorMessages)], Response::HTTP_BAD_REQUEST);
        }
        $client = $this->getInfoClientInputPort->getInfo($uuidClient);

        if ($client !=  null) {
            // convertir la entidad en array antes de devolverla
            return new JsonResponse(['client' => [
                'name' => $client->getName(),
                'email' => $client->getCompanyEmail(),
                'phone' => $client->getCompanyPhone(),
                'nifCif' => $client->getNifCif(),
                'fiscalAddress' => $client->getFiscalAddress(),
                'physicalAddress' => $client->getPhysicalAddress(),
                'city' => $client->getCity(),
                'country' => $client->getCountry(),
                'postalCode' => $client->getPostalCode()

            ]], Response::HTTP_OK);
        }

        return new JsonResponse(['error' => 'Client not found'], Response::HTTP_NOT_FOUND);
    }
}