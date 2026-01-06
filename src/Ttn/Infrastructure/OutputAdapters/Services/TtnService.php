<?php

namespace App\Ttn\Infrastructure\OutputAdapters\Services;

use App\Entity\Main\TtnApps;
use App\Ttn\Application\DTO\RegisterTtnAppRequest;
use App\Ttn\Application\DTO\RegisterTtnAppResponse;
use App\Ttn\Application\DTO\RegisterTtnDeviceRequest;
use App\Ttn\Application\OutputPorts\TtnServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
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
    private string $apiUserKey;
    private string $apiAppKey;
    private EntityManagerInterface $entityManager;

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
        string $apiUserKey,
        string $apiAppKey,
        EntityManagerInterface $entityManager,
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
        $this->apiUserKey = $apiUserKey;
        $this->apiAppKey = $apiAppKey;
        $this->entityManager = $entityManager;
    }

    public function registerApp(RegisterTtnAppRequest $request): RegisterTtnAppResponse
    {
        $applicationId = $request->getApplicationId();
        $name = $request->getName();
        $description = $request->getDescription();
        $appUserKey = true;
        // Validar los datos básicos
        if (empty($applicationId) || empty($name) || empty($description)) {
            return new RegisterTtnAppResponse(false, 'application_id, name, and description are required.');
        }

        try {
            $isPayloadCreatedApp = [
                'application' => [
                    'ids' => [
                        'application_id' => $applicationId,
                    ],
                    'name' => $name,
                    'description' => $description,
                    'network_server_address' => $this->networkServerAddress,
                    'application_server_address' => $this->applicationServerAddress,
                    'join_server_address' => $this->joinServerAddress,
                ],
            ];
            $this->logger->info('Payload para TTN API:', ['data' => $isPayloadCreatedApp]);
            $this->apiClient->post('/users/santiagofragio/applications', $isPayloadCreatedApp, $appUserKey);

            // Crear la entidad de la tabla TTN_APPS y guardar en BBDD
            $ttnApp = new TtnApps();
            $ttnApp->setUuidClient($request->getUuidClient());
            $ttnApp->setUuidUserCreation($request->getUuidUserCreation());
            $ttnApp->setDatehourCreation($request->getDatehourCreation());
            $ttnApp->setTtnApplicationId($applicationId);
            $ttnApp->setTtnApplicationName($name);
            $ttnApp->setTtnApplicationDescription($description);
            $ttnApp->setNetworkServerAddress($this->networkServerAddress);
            $ttnApp->setApplicationServerAddress($this->applicationServerAddress);
            $ttnApp->setJoinServerAddress($this->joinServerAddress);

            $this->entityManager->persist($ttnApp);
            $this->entityManager->flush();

            return new RegisterTtnAppResponse(true);
        } catch (\Exception $e) {
            $this->logger->error('Error registering app: '.$e->getMessage());

            return new RegisterTtnAppResponse(false, $e->getMessage());
        }
    }

    public function registerDevice(RegisterTtnDeviceRequest $request): void
    {
        $deviceId = $request->getDeviceId();
        $devEui = $request->getDevEui();   // Generar EUI si no lo proporciona el front
        $joinEui = $request->getJoinEui();
        $appKey = $request->getAppKey();

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

            $this->apiClient->post("/applications/{$this->applicationId}/devices", $isPayload, false);

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
                        // 'ids.join_eui' is read-only, already set in Identity Server
                        'root_keys.app_key.key',
                    ],
                ],
            ];

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
                        // 'ids.join_eui' is read-only, already set in Identity Server
                        'lorawan_phy_version',
                        'frequency_plan_id',
                    ],
                ],
            ];

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
                    // 'ids.join_eui' is read-only, already set in Identity Server
                    'paths' => ['ids.device_id', 'ids.dev_eui'],
                ],
            ];

            $this->apiClient->put("/as/applications/{$this->applicationId}/devices/{$deviceId}", $asPayload);
            $this->logger->info('Device register successfully');
        } catch (\Exception $e) {
            $this->logger->error('Error registering device: '.$e->getMessage());

            // Podrías lanzar tu propia excepción para que el UseCase sepa que falló
            throw new \RuntimeException('Error unassigning device in TTN', 0, $e);
        }
    }

    public function unassignDevice(string $deviceId): void
    {
        try {
            $isPayload = [
                'end_device' => [
                    'name' => 'free',
                ],
                'field_mask' => [
                    'paths' => [
                        'name',
                    ],
                ],
            ];

            $this->apiClient->put("/applications/{$this->applicationId}/devices/{$deviceId}", $isPayload);
            $this->logger->info('Device unassigned successfully');
        } catch (\Exception $e) {
            $this->logger->error('Error unassigning device in TTN: '.$e->getMessage());

            // Podrías lanzar tu propia excepción para que el UseCase sepa que falló
            throw new \RuntimeException('Error unassigning device in TTN', 0, $e);
        }
    }
}
