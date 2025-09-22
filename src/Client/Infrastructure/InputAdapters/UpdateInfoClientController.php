<?php

namespace App\Client\Infrastructure\InputAdapters;

use App\Client\Application\DTO\UpdateInfoClientRequest;
use App\Client\Application\InputPorts\UpdateInfoClientInputPort;
use App\Client\Application\UseCases\UpdateInfoClientUseCase;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateInfoClientController extends AbstractController
{
    use PermissionControllerTrait;

    private UpdateInfoClientInputPort $updateInfoClientInputPort;
    private UpdateInfoClientUseCase $updateInfoClientUseCase;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private LoggerInterface $logger;

    public function __construct(UpdateInfoClientInputPort $updateInfoClientInputPort,
        UpdateInfoClientUseCase $updateInfoClientUseCase,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        LoggerInterface $logger,
        PermissionService $permissionService)
    {
        $this->updateInfoClientInputPort = $updateInfoClientInputPort;
        $this->updateInfoClientUseCase = $updateInfoClientUseCase;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/client_update', name: 'api_client_update', methods: ['POST'])]
    #[RequiresPermission('client.view')]
    public function updateInfoClient(Request $request): JsonResponse
    {
        $permissionCheck = $this->checkPermissionJson('client.view', 'No tienes permisos para crear un producto');
        if ($permissionCheck) {
            return $permissionCheck;
        }

        try {
            // 1) Deserializar el JSON al DTO UpdateProductRequest
            $updateRequest = $this->serializer->deserialize(
                $request->getContent(),
                UpdateInfoClientRequest::class,
                'json');
            //die("llegamos");
            // 2) Validar el DTO con Symfony Validator
            $errors = $this->validator->validate($updateRequest);
            if (count($errors) > 0) {
                $errorMessages = $this->formatValidationErrors($errors);

                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'INVALID_DATA',
                    'errors' => $errorMessages,
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            // 3) Asignar manualmente (porque el user no llega en el JSON)
            $user = $this->getUser();
            if (!$user) {
                return $this->jsonError('USER_NOT_AUTHENTICATED', JsonResponse::HTTP_UNAUTHORIZED);
            }
            //die("llegamos 2");
            // 4) Asignar el userModification
            $updateRequest->setUuidUserModification($user->getUuid());

            // 5) Asignar fecha de modificación
            $updateRequest->setDatehourModification(new \DateTime());

            // 6) Ejecutar el caso de uso
            $response = $this->updateInfoClientUseCase->execute($updateRequest);

            // 7) Respuesta exitosa
            return new JsonResponse([
                'status' => 'success',
                'message' => 'CLIENT_UPDATED_SUCCESSFULLY',
                'client' => $response->getClient(), // Por ej. array con uuid, name...
            ], $response->getStatusCode());
        } catch (\RuntimeException $e) {
            // Errores de dominio esperados (p.ej. "CLIENT_NOT_FOUND", "CLIENT_NOT_FOUND", etc.)
            if ('CLIENT_NOT_FOUND' === $e->getMessage()) {
                return $this->jsonError('CLIENT_NOT_FOUND', JsonResponse::HTTP_NOT_FOUND);
            }
            if ('CLIENT_NOT_FOUND' === $e->getMessage()) {
                return $this->jsonError('CLIENT_NOT_FOUND', JsonResponse::HTTP_NOT_FOUND);
            }
            if ('USER_NOT_AUTHENTICATED' === $e->getMessage()) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'USER_NOT_AUTHENTICATED',
                ], Response::HTTP_UNAUTHORIZED);
            }
            // etc. Manejo 403, 409, etc.

            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            // Errores inesperados
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Formatea la lista de errores de validación en un array asociativo.
     */
    private function formatValidationErrors(ConstraintViolationListInterface $errors): array
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $field = $error->getPropertyPath();
            $message = $error->getMessage();
            $errorMessages[$field] = $message;
        }

        return $errorMessages;
    }
}
