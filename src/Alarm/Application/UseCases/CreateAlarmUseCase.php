<?php

// src/Alarm/Application/UseCases/CreateAlarmUseCase.php

namespace App\Alarm\Application\UseCases;

use App\Alarm\Application\DTO\CreateAlarmRequest;
use App\Alarm\Application\DTO\CreateAlarmResponse;
use App\Alarm\Application\InputPorts\CreateAlarmUseCaseInterface;
use App\Alarm\Application\OutputPorts\Repositories\AlarmRepositoryInterface;
use App\Alarm\Application\OutputPorts\Repositories\AlarmTypeRepositoryInterface;
use App\Alarm\Infrastructure\OutputAdapters\Repositories\AlarmConfigRepository;
use App\Alarm\Infrastructure\OutputAdapters\Repositories\AlarmTypeRepository;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\AlarmConfig;
use App\Infrastructure\Services\ClientConnectionManager;
use Psr\Log\LoggerInterface;

class CreateAlarmUseCase implements CreateAlarmUseCaseInterface
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private AlarmRepositoryInterface $alarmRepository;
    private AlarmTypeRepositoryInterface $alarmTypeRepository;
    private ClientRepositoryInterface $clientRepository;

    public function __construct(
        ClientConnectionManager $connectionManager,
        LoggerInterface $logger,
        AlarmRepositoryInterface $alarmRepository,
        AlarmTypeRepositoryInterface $alarmTypeRepository,
        ClientRepositoryInterface $clientRepository,
    ) {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
        $this->alarmRepository = $alarmRepository;
        $this->alarmTypeRepository = $alarmTypeRepository;
        $this->clientRepository = $clientRepository;
    }

    public function execute(CreateAlarmRequest $request): CreateAlarmResponse
    {
        // 1) Comprobar si el cliente existe
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if (!$client) {
            // Lanza \RuntimeException('CLIENT_NOT_FOUND')
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        // 2) Obtener el EntityManager para el cliente
        $em = $this->connectionManager->getEntityManager($client->getUuidClient());
        // 3) Buscar el tipo de alarma según el valor proporcionado en el request,
        // usando un repositorio instanciado con el EntityManager del cliente.
        $alarmType = (new AlarmTypeRepository($em))
            ->findByType($request->getType());
        if (!$alarmType) {
            throw new \RuntimeException('ALARM_TYPE_NOT_FOUND');
        }

        // 4) Crear la entidad AlarmConfig y poblarla con los datos del request
        $alarmRepository = new AlarmConfigRepository($em);
        $alarmConfig = new AlarmConfig();
        $alarmConfig->setAlarmName($request->getName());
        // Aquí deberás asignar el id del producto al que se aplica la alarma.
        // Si el request no lo incluye, podrías asignar un valor por defecto o lanzar error.
        // Ejemplo: $alarmConfig->setProductId($request->getProductId());
        // Por ahora, asignamos 0 como placeholder:
        $alarmConfig->setProductId(0);
        $alarmConfig->setAlarmType($alarmType);
        $alarmConfig->setPercentageThreshold($request->getPercentageThreshold());
        $alarmConfig->setUuidUserCreation($request->getUuidUserCreation());
        $alarmConfig->setCreationDate($request->getDatehourCreation());

        // 5) Persistir la entidad usando el repositorio inyectado
        $alarmRepository->save($alarmConfig);

        // 6) Obtener el EntityManager para la DB central (del CRON)
        $emCron = $this->connectionManager->getCentralEntityManager(); // Debes implementar este método

        // 7) Crear la réplica en la DB central
        $clientAlarmConfig = new \App\Entity\Cron\ClientAlarmConfig();
        $clientAlarmConfig->setClientId($client->getUuidClient());
        $clientAlarmConfig->setProductId($alarmConfig->getProductId());
        // $clientAlarmConfig->setAlarmName($alarmConfig->getAlarmName());
        $clientAlarmConfig->setAlarmType($request->getType());
        $clientAlarmConfig->setAlarmThreshold($alarmConfig->getPercentageThreshold());
        // Asigna otros campos que consideres necesarios...

        $emCron->persist($clientAlarmConfig);
        $emCron->flush();

        $alarmData = [
            'id' => $alarmConfig->getId(),
            'name' => $alarmConfig->getAlarmName(),
        ];

        return new CreateAlarmResponse($alarmData, null, 200);
    }
}
