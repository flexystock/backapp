<?php

namespace App\Client\Application;

use App\Entity\Main\Client;
use App\Client\Infrastructure\InputPorts\CreateClientInputPort;
use App\Client\Infrastructure\OutputPorts\ClientRepositoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Caso de uso para la creación de clientes.
 */
class CreateClientUseCase implements CreateClientInputPort
{
    /**
     * Repositorio de clientes.
     * @var ClientRepositoryInterface
     */
    private ClientRepositoryInterface $clientRepository;

    /**
     * Servicio de validación.
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * Constructor.
     * @param ClientRepositoryInterface $clientRepository El repositorio de clientes.
     * @param ValidatorInterface $validator El servicio de validación.
     */
    public function __construct(ClientRepositoryInterface $clientRepository, ValidatorInterface $validator)
    {
        $this->clientRepository = $clientRepository;
        $this->validator = $validator;
    }

    /**
     * Crea un nuevo cliente.
     * @param array $data Los datos del cliente.
     * @return Client El cliente creado.
     * @throws \Exception Si hay errores de validación.
     */
    public function create(array $data): Client
    {
        $client = new Client();
        $uuid = Uuid::v4()->toRfc4122();
        $client->setUuid($uuid);
        $client->setName($data['name']);
        $scheme = $data['name']."_DATABASE_URL";
        $scheme = strtoupper($scheme);
        $client->setScheme($scheme);

        $errors = $this->validator->validate($client);
        if (count($errors) > 0) {
            throw new \Exception((string)$errors);
        }

        //guardamos en BBDD
        $this->clientRepository->save($client);

        return $client;
    }
}