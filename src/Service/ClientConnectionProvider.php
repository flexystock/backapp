<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ClientConnectionProvider
{
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function getConnectionParams(string $uuidClient): array
    {
        // Implementa la lógica para obtener los parámetros de conexión basados en uuidClient
        // Ejemplo simple:
        return [
            'dbname' => 'db_' . $uuidClient,
            'user' => 'user_' . $uuidClient,
            'password' => 'password_' . $uuidClient,
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        ];
    }
}
//namespace App\Service;
//
//use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
//use App\Client\Infrastructure\OutputAdapters\Repositories\ClientRepository;
//use Doctrine\DBAL\Exception;
//
//class ClientConnectionProvider
//{
//    // rivate ClientRepository $clientRepository;
//
//    private ClientRepositoryInterface $clientRepository;
//
//    public function __construct(ClientRepositoryInterface $clientRepository)
//    {
//        $this->clientRepository = $clientRepository;
//    }
//
//    public function getConnectionParams(string $uuidClient): array
//    {
//        //        // Fetch client-specific database configuration
//                $clientConfig = $this->clientRepository->findOneBy(['uuidClient' => $uuidClient]);
//
//                if (!$clientConfig) {
//                    throw new Exception('Client configuration not found');
//                }
//
//                return [
//                    'dbname' => $clientConfig->getDatabaseName(),
//                    'user' => $clientConfig->getDatabaseUserName(),
//                    'password' => $clientConfig->getDatabasePassword(),
//                    'host' => $clientConfig->getHost(),
//                    'port' => $clientConfig->getPortBbdd(),
//                    'driver' => 'pdo_mysql',
//                ];
////        $client = $this->clientRepository->findByUuid($uuidClient);
////
////        if (!$client) {
////            throw new \InvalidArgumentException("Client with UUID $uuidClient not found.");
////        }
////
////        // Supongamos que tu entidad Client tiene los siguientes métodos:
////        // getDbHost(), getDbPort(), getDbName(), getDbUser(), getDbPassword()
////        return [
////            'dbname' => $client->getDbName(),
////            'user' => $client->getDbUser(),
////            'password' => $client->getDbPassword(),
////            'host' => $client->getDbHost(),
////            'port' => $client->getDbPort(),
////            'driver' => 'pdo_mysql',
////            // Añade otros parámetros necesarios aquí
////        ];
//    }
//}
