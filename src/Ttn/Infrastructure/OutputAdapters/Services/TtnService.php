<?php

// src/Ttn/Infrastructure/OutputAdapters/Services/TtnService.php

namespace App\Ttn\Infrastructure\OutputAdapters\Services;

use App\Ttn\Application\DTO\RegisterDeviceRequest;
use App\Ttn\Application\DTO\RegisterDeviceResponse;
use App\Ttn\Application\OutputPorts\TtnServiceInterface;
use Psr\Log\LoggerInterface;

class TtnService implements TtnServiceInterface
{
    private TtnApiClient $apiClient;
    private LoggerInterface $logger;
    private string $applicationId;
    private string $networkServerAddress;
    private string $applicationServerAddress;
    private string $joinServerAddress;
    private string $lorawanVersion;
    private string $lorawanPhyVersion;
    private string $frequencyPlanId;

    public function __construct(
        TtnApiClient $apiClient,
        LoggerInterface $logger,
        string $applicationId,
        string $networkServerAddress,
        string $applicationServerAddress,
        string $joinServerAddress,
        string $lorawanVersion,
        string $lorawanPhyVersion,
        string $frequencyPlanId,
    ) {
        $this->apiClient = $apiClient;
        $this->logger = $logger;
        $this->applicationId = $applicationId;
        $this->networkServerAddress = $networkServerAddress;
        $this->applicationServerAddress = $applicationServerAddress;
        $this->joinServerAddress = $joinServerAddress;
        $this->lorawanVersion = $lorawanVersion;
        $this->lorawanPhyVersion = $lorawanPhyVersion;
        $this->frequencyPlanId = $frequencyPlanId;
    }

    public function registerDevice(RegisterDeviceRequest $request): RegisterDeviceResponse
    {
        $deviceId = $request->getDeviceId();
        $devEui = $request->getDevEui() ?? $this->generateEui();   // Generar EUI si no lo proporciona el front
        var_dump($devEui);
        var_dump("hola");
        $joinEui = $request->getJoinEui() ?? $this->generateEui();
        var_dump($joinEui);
        var_dump("hola");
        $appKey = $request->getAppKey() ?? $this->generateAppKey();
        var_dump($appKey);
        var_dump("hola");

        try {
            // 1. Identity Server
            $isPayload = [
                'end_device' => [
                    'ids' => [
                        'device_id' => $deviceId,
                        'dev_eui' => $devEui,
                        'join_eui' => $joinEui,
                    ],
                    'join_server_address' => $this->joinServerAddress,
                    'network_server_address' => $this->networkServerAddress,
                    'application_server_address' => $this->applicationServerAddress,
                ],
                'field_mask' => [
                    'paths' => [
                        'join_server_address',
                        'network_server_address',
                        'application_server_address',
                        'ids.dev_eui',
                        'ids.join_eui',
                    ],
                ],
            ];
            var_dump($isPayload);
            var_dump("hola");
            $this->apiClient->post("/applications/{$this->applicationId}/devices", $isPayload);

            // 2. Join Server
            $jsPayload = [
                'end_device' => [
                    'ids' => [
                        'device_id' => $deviceId,
                        'dev_eui' => $devEui,
                        'join_eui' => $joinEui,
                    ],
                    'network_server_address' => $this->networkServerAddress,
                    'application_server_address' => $this->applicationServerAddress,
                    'root_keys' => [
                        'app_key' => ['key' => $appKey],
                    ],
                ],
                'field_mask' => [
                    'paths' => [
                        'network_server_address',
                        'application_server_address',
                        'ids.device_id',
                        'ids.dev_eui',
                        'ids.join_eui',
                        'root_keys.app_key.key',
                    ],
                ],
            ];
            var_dump($jsPayload);
            var_dump("hola");
            $this->apiClient->put("/js/applications/{$this->applicationId}/devices/{$deviceId}", $jsPayload);

            // 3. Network Server
            $nsPayload = [
                'end_device' => [
                    'supports_join' => true,
                    'lorawan_version' => $this->lorawanVersion,
                    'lorawan_phy_version' => $this->lorawanPhyVersion,
                    'frequency_plan_id' => $this->frequencyPlanId,
                    'ids' => [
                        'device_id' => $deviceId,
                        'dev_eui' => $devEui,
                        'join_eui' => $joinEui,
                    ],
                ],
                'field_mask' => [
                    'paths' => [
                        'supports_join',
                        'lorawan_version',
                        'ids.device_id',
                        'ids.dev_eui',
                        'ids.join_eui',
                        'lorawan_phy_version',
                        'frequency_plan_id',
                    ],
                ],
            ];
            var_dump($nsPayload);
            var_dump("hola");
            $this->apiClient->put("/ns/applications/{$this->applicationId}/devices/{$deviceId}", $nsPayload);

            // 4. Application Server
            $asPayload = [
                'end_device' => [
                    'ids' => [
                        'device_id' => $deviceId,
                        'dev_eui' => $devEui,
                        'join_eui' => $joinEui,
                    ],
                ],
                'field_mask' => [
                    'paths' => ['ids.device_id', 'ids.dev_eui', 'ids.join_eui'],
                ],
            ];
            var_dump($asPayload);
            var_dump("hola");
            $this->apiClient->put("/as/applications/{$this->applicationId}/devices/{$deviceId}", $asPayload);

            return new RegisterDeviceResponse(true);
        } catch (\Exception $e) {
            $this->logger->error('Error registering device: '.$e->getMessage());

            return new RegisterDeviceResponse(false, $e->getMessage());
        }
    }

    private function generateEui(): string
    {
        // Genera un EUI de 8 bytes en hex
        return strtoupper(bin2hex(random_bytes(8)));
    }

    private function generateAppKey(): string
    {
        // Genera un AppKey de 16 bytes en hex
        return strtoupper(bin2hex(random_bytes(16)));
    }
}
