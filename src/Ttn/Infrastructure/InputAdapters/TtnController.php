<?php
// src/Ttn/Infrastructure/InputAdapters/TtnController.php
namespace App\Ttn\Infrastructure\InputAdapters;

use App\Ttn\Application\DTO\RegisterDeviceRequest;
use App\Ttn\Application\InputPorts\RegisterDeviceUseCaseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TtnController extends AbstractController
{
    private RegisterDeviceUseCaseInterface $registerDeviceUseCase;

    public function __construct(RegisterDeviceUseCaseInterface $registerDeviceUseCase)
    {
        $this->registerDeviceUseCase = $registerDeviceUseCase;
    }

    #[Route('/api/device_register', name: 'api_device_register', methods: ['POST'])]
    public function registerDevice(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $deviceId = $data['device_id'] ?? null;
        //die("llegamos");
        if (!$deviceId) {
            return new JsonResponse(['error' => 'device_id is required'], 400);
        }

        $dto = new RegisterDeviceRequest($deviceId);
        $response = $this->registerDeviceUseCase->execute($dto);

        if ($response->isSuccess()) {
            return new JsonResponse(['message' => 'Device registered successfully'], 200);
        } else {
            return new JsonResponse(['error' => $response->getError()], 500);
        }
    }

    #[Route('/api/get_ttn_apps', name: 'get_ttn_apps', methods: ['GET'])]
    public function getTtnApps(): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $deviceId = $data['device_id'] ?? null;
        //die("llegamos");
        if (!$deviceId) {
            return new JsonResponse(['error' => 'device_id is required'], 400);
        }

        $dto = new RegisterDeviceRequest($deviceId);
        $response = $this->registerDeviceUseCase->execute($dto);

        if ($response->isSuccess()) {
            return new JsonResponse(['message' => 'Device registered successfully'], 200);
        } else {
            return new JsonResponse(['error' => $response->getError()], 500);
        }
    }
}