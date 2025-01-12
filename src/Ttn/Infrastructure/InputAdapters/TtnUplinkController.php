<?php

namespace App\Ttn\Infrastructure\InputAdapters;

use App\Ttn\Application\DTO\TtnUplinkRequest;
use App\Ttn\Application\InputPorts\HandleTtnUplinkUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TtnUplinkController extends AbstractController
{
    private LoggerInterface $logger;
    private HandleTtnUplinkUseCaseInterface $handleTtnUplinkUseCase;

    public function __construct(LoggerInterface $logger, HandleTtnUplinkUseCaseInterface $handleTtnUplinkUseCase)
    {
        $this->logger = $logger;
        $this->handleTtnUplinkUseCase = $handleTtnUplinkUseCase;
    }

    #[Route('/api/ttn-uplink', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        // 1) Obtener el contenido (JSON crudo)
        $content = $request->getContent();

        // 2) Parsear a array asociativo
        $data = json_decode($content, true);

        // 3) Extraer los campos que te interesan
        $deviceId = $data['end_device_ids']['device_id'] ?? null;
        $devEui = $data['end_device_ids']['dev_eui'] ?? null;
        $joinEui = $data['end_device_ids']['join_eui'] ?? null;

        // En “decoded_payload” están los valores “voltage” y “weight”
        $voltage = $data['uplink_message']['decoded_payload']['voltage'] ?? null;
        $weight = $data['uplink_message']['decoded_payload']['weight'] ?? null;

        // 4) Loguear o trabajar con esas variables
        $this->logger->info('Campos relevantes de TTN', [
            'device_id' => $deviceId,
            'dev_eui' => $devEui,
            'join_eui' => $joinEui,
            'voltage' => $voltage,
            'weight' => $weight,
        ]);

        // 5) Sigue con tu lógica: guardarlo en BBDD, etc.
        $uplinkRequest = new TtnUplinkRequest($devEui, $deviceId, $joinEui, $voltage, $weight);
        $this->handleTtnUplinkUseCase->execute($uplinkRequest);

        // 4) Retornar 200 o la que TTN necesite
        return new JsonResponse(['status' => 'ok'], 200);
    }
}
