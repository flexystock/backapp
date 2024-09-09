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
    public function create(string $name): Client
    {
        $client = new Client();
        $uuid = Uuid::v4()->toRfc4122();
        $client->setUuid($uuid);
        $client->setName($name);
        $databaseName = 'database_'.strtoupper($name);
        $client->setDatabaseName($databaseName);

        // Determinar el puerto para el nuevo contenedor
        $port = $this->findAvailablePort(40010);
        $client->setPort($port);

        $errors = $this->validator->validate($client);
        if (count($errors) > 0) {
            throw new \Exception((string)$errors);
        }

        // Guardar en la base de datos
        $this->clientRepository->save($client);

        // Verificar la ruta de trabajo actual y si el script existe
        $currentDir = getcwd();
        $scriptPath = '/appdata/www/bin/create_client_container.sh';
        if (!file_exists($scriptPath)) {
            throw new \Exception("Script not found: " . $scriptPath);
        }

        // Llamar al script para crear el contenedor y actualizar el .env
        $command = sprintf('bash %s %s %d 2>&1', escapeshellarg($scriptPath), escapeshellarg($data['clientName']), $port);
        exec($command, $output, $return_var);

        if ($return_var !== 0) {
            // Registrar el error y la salida del comando
            error_log('Error creating client container: ' . implode("\n", $output));
            throw new \Exception('Error creating client container: ' . implode("\n", $output));
        }

        return $client;
    }

    private function findAvailablePort($startPort): int
    {
        $port = $startPort;
        while ($this->isPortInUse($port)) {
            $port++;
            if ($port > 65535) { // Evitar ciclos infinitos
                $port = 40010; // Reiniciar el ciclo si se superan los puertos disponibles
            }
        }
        return $port;
    }

    private function isPortInUse($port): bool
    {
        // Consultar la base de datos para ver si el puerto está en uso
        $client = $this->clientRepository->findOneBy(['port' => $port]);
        if ($client !== null) {
            return true;
        }

        // Verificar si el puerto está en uso en el sistema
        $connection = @fsockopen('localhost', $port);
        if (is_resource($connection)) {
            fclose($connection);
            return true;
        }
        return false;
    }
}