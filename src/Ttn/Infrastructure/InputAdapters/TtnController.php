<?php

namespace App\Ttn\Infrastructure\InputAdapters;

use App\Client\Application\InputPorts\GetClientByUuidInputPort;
use App\Ttn\Application\DTO\RegisterTtnAppRequest;
use App\Ttn\Application\InputPorts\RegisterTtnAppUseCaseInterface;
use App\Ttn\Application\InputPorts\RegisterTtnDeviceUseCaseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TtnController extends AbstractController
{
    private RegisterTtnDeviceUseCaseInterface $registerDeviceUseCase;
    private RegisterTtnAppUseCaseInterface $registerAppTtnUseCase;
    private GetClientByUuidInputPort $getClientByUuidInputPort;

    public function __construct(RegisterTtnDeviceUseCaseInterface $registerDeviceUseCase, RegisterTtnAppUseCaseInterface $registerAppTtnUseCase,
                                GetClientByUuidInputPort          $getClientByUuidInputPort)
    {
        $this->registerDeviceUseCase = $registerDeviceUseCase;
        $this->registerAppTtnUseCase = $registerAppTtnUseCase;
        $this->getClientByUuidInputPort = $getClientByUuidInputPort;
    }

    #[Route('/api/app_register', name: 'api_app_register', methods: ['POST'])]
    public function registerTtnApp(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }
        $uuidClient = $data['uuid_client'] ?? null;
        $client = $this->getClientByUuidInputPort->getByUuid($uuidClient);
        if (!$client) {
            return new JsonResponse(['error' => 'Client not found'], Response::HTTP_BAD_REQUEST);
        }

        $applicationId = 'application-id-'.$client->getClientName();
        $applicationName = 'application-Name-'.$client->getClientName();
        $description = 'description-'.$client->getClientName();
        $dto = new RegisterTtnAppRequest(
            $applicationId,
            $applicationName,
            $description);
        $response = $this->registerAppTtnUseCase->execute($dto);

        if ($response->isSuccess()) {
            return new JsonResponse(['message' => 'Application registered successfully'], 200);
        } else {
            return new JsonResponse(['error' => $response->getError()], 500);
        }
    }

    #[Route('/api/device_register', name: 'api_device_register', methods: ['POST'])]
    public function registerTtnDevice(Request $request): JsonResponse
    {
        return $this->extracted($request);
    }

    #[Route('/api/get_ttn_apps', name: 'get_ttn_apps', methods: ['GET'])]
    public function getAllTtnApps(Request $request): JsonResponse
    {
        return $this->extracted($request);
    }

    public function extracted(Request $request): JsonResponse
    {
        return json_decode($request->getContent(), true);
    }

    public function getTtnApp(Request $request): JsonResponse
    {
        return $this->extracted($request);
    }

    public function getTtnDevice(Request $request): JsonResponse
    {
        return $this->extracted($request);
    }

    public function deleteTtnApp(Request $request): JsonResponse
    {
        return $this->extracted($request);
    }

    public function deleteTtnDevice(Request $request): JsonResponse
    {
        return $this->extracted($request);
    }

    public function getAllTtnDevices(Request $request): JsonResponse
    {
        return $this->extracted($request);
    }
}
