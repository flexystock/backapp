<?php

namespace App\Client\Application\UseCases;
use App\Entity\Main\ClientHistory;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Client\Application\DTO\UpdateInfoClientRequest;
use App\Client\Application\DTO\UpdateInfoClientResponse;
use App\Client\Application\InputPorts\UpdateInfoClientInputPort;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

class UpdateInfoClientUseCase implements UpdateInfoClientInputPort
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private $clientRepository;
    private $mainEntityManager;



    public function __construct (ClientConnectionManager $connectionManager,
                                 LoggerInterface $logger,
                                 $clientRepository,
                                 EntityManagerInterface $mainEntityManager
    )
    {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
        $this->clientRepository = $clientRepository;
        $this->mainEntityManager = $mainEntityManager;
    }

    public function execute (UpdateInfoClientRequest $request): UpdateInfoClientResponse
    {
        $uuidClient = $request->getUuidClient();

        if (!$uuidClient) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        try {
            // 1. Buscar el cliente
            $client = $this->clientRepository->findByUuid($uuidClient);
            if (!$client) {
                throw new \RuntimeException('CLIENT_NOT_FOUND');
            }
            $em = $this->connectionManager->getEntityManager($uuidClient);

            //die("este es el cliente");
            // Comprobación ejemplo de nombre duplicado, si necesitas ese control.
            // if ($request->getName() && $existing = $this->clientRepository->findOneBy(['name' => $request->getName()])) {
            //     if ($existing->getUuid() !== $uuidClient) {
            //         throw new \RuntimeException('CLIENT_DUPLICATED');
            //     }
            // }

            // 2. Datos antes de modificación
            $beforeData = [
                'name' => $client->getName(),
                'companyEmail' => $client->getCompanyEmail(),
                'companyPhone' => $client->getCompanyPhone(),
                'nif/cif' => $client->getNifCif(),
                'fiscalAddress' => $client->getFiscalAddress(),
                'physicalAddress' => $client->getPhysicalAddress(),
                'city' => $client->getCity(),
                'country' => $client->getCountry(),
                'postalCode' => $client->getPostalCode(),
                // Añade los campos que consideres
            ];

            $beforeJson = json_encode($beforeData);

            // 3. Actualiza los campos del cliente solo si el request los tiene no-nulos
            if (null !== $request->getName()) {
                $client->setName($request->getName());
            }
            if (null !== $request->getCompanyEmail()) {
                $client->setCompanyEmail($request->getCompanyEmail());
            }
            if (null !== $request->getCompanyPhone()) {
                $client->setCompanyPhone($request->getCompanyPhone());
            }
            if (null !== $request->getNifCif()) {
                $client->setNifCif($request->getNifCif());
            }
            if (null !== $request->getFiscalAddress()) {
                $client->setFiscalAddress($request->getFiscalAddress());
            }
            if (null !== $request->getPhysicalAddress()) {
                $client->setPhysicalAddress($request->getPhysicalAddress());
            }
            if (null !== $request->getCity()) {
                $client->setCity($request->getCity());
            }
            if (null !== $request->getCountry()) {
                $client->setCountry($request->getCountry());
            }
            if (null !== $request->getPostalCode()) {
                $client->setPostalCode($request->getPostalCode());
            }
            // ... otros campos ...

            // 4. Datos después de modificación
            $afterData = [
                'name' => $request->getName(),
                'companyEmail' => $request->getCompanyEmail(),
                'companyPhone' => $request->getCompanyPhone(),
                'nif/cif' => $request->getNifCif(),
                'fiscalAddress' => $request->getFiscalAddress(),
                'physicalAddress' => $request->getPhysicalAddress(),
                'city' => $request->getCity(),
                'country' => $request->getCountry(),
                'postalCode' => $request->getPostalCode(),
            ];

            $afterJson = json_encode($afterData);

            // 5. Guardar historial en la BBDD main

            $history = new ClientHistory();
            $history->setUuidClient($client->getUuidClient());
            $history->setUuidUserModification($request->getUuidUserModification());
            $history->setDataClientBeforeModification($beforeJson);
            $history->setDataClientAfterModification($afterJson);
            $history->setDateModification(new \DateTime());
            $this->mainEntityManager->persist($history);
            $this->mainEntityManager->flush();


            // 6. Guarda el cliente modificado en su base de datos
            $this->mainEntityManager->persist($client);
            $this->mainEntityManager->flush();


            // 7. Devuelve respuesta
            $clientData = [
                'name' => $client->getName(),
                'email' => $client->getCompanyEmail(),
                'phone' => $client->getCompanyPhone(),
                'nifCif' => $client->getNifCif(),
                'fiscalAddress' => $client->getFiscalAddress(),
                'physicalAddress' => $client->getPhysicalAddress(),
                'city' => $client->getCity(),
                'country' => $client->getCountry(),
                'postalCode' => $client->getPostalCode()
                // otros campos...
            ];
            return new UpdateInfoClientResponse($clientData, null, 200);

        } catch (\Exception $e) {
            $this->logger->error('UpdateInfoClientUseCase: Error updating client.', [
                'uuid_client' => $uuidClient,
                'exception' => $e,
            ]);
            return new UpdateInfoClientResponse(null, 'Internal Server Error', 500);
        }
    }
}