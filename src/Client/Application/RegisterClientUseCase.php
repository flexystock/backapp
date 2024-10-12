<?php

namespace App\Client\Application;

use App\Entity\Main\Client;
use App\Client\Infrastructure\InputPorts\RegisterClientInputPort;
use App\Client\Infrastructure\OutputPorts\ClientRepositoryInterface;
use Cassandra\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Client\Application\DTO\RegisterClientRequest;
use App\User\Infrastructure\OutputPorts\UserRepositoryInterface;
use App\Entity\Main\User;
use App\Service\DockerService;
use App\Message\CreateDockerContainerMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use App\User\Application\DTO\CreateUserRequest;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\User\Infrastructure\OutputPorts\NotificationServiceInterface;

/**
 * Caso de uso para la creación de clientes.
 */
class RegisterClientUseCase implements RegisterClientInputPort
{

    private ClientRepositoryInterface $clientRepository;
    private UserRepositoryInterface $userRepository;
    private ValidatorInterface $validator;
    private DockerService $dockerService;
    private MessageBusInterface $bus;
    private MailerInterface $mailer;
    private UrlGeneratorInterface $urlGenerator;
    private NotificationServiceInterface $notificationService;
    public function __construct(ClientRepositoryInterface $clientRepository,
                                ValidatorInterface $validator,
                                UserRepositoryInterface $userRepository,
                                DockerService $dockerService,
                                MessageBusInterface $bus,
                                MailerInterface $mailer,
                                UrlGeneratorInterface $urlGenerator,
                                NotificationServiceInterface $notificationService)
    {
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
        $this->validator = $validator;
        $this->dockerService = $dockerService;
        $this->bus = $bus;
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->notificationService = $notificationService;
    }

    /**
     * @throws \DateMalformedStringException
     * @throws \Exception
     */
    public function register(RegisterClientRequest $request): Client
    {
        $client = new Client();//uuiCLient y User_id

        $client->setName($request->getName());
        $client->setClientName($request->getName());
        $client->setBusinessType($request->getBusinessType());
        $client->setFiscalAddress($request->getFiscalAddress());
        $client->setCity($request->getCity());
        $client->setCountry($request->getCountry());
        $client->setPostalCode($request->getPostalCode());
        $client->setCompanyPhone($request->getCompanyPhone());

        // Establecer los demás campos opcionales
        $client->setNifCif($request->getNifCif());
        if ($request->getFoundationDate()) {
            $client->setFoundationDate(new \DateTime($request->getFoundationDate()));
        }
        $client->setPhysicalAddress($request->getPhysicalAddress());
        $client->setCompanyEmail($request->getCompanyEmail());
        $client->setNumberOfEmployees($request->getNumberOfEmployees());
        $client->setIndustrySector($request->getIndustrySector());
        $client->setAverageInventoryVolume($request->getAverageInventoryVolume());
        $client->setCurrency($request->getCurrency());
        $client->setPreferredPaymentMethods($request->getPreferredPaymentMethods());
        $client->setOperationHours($request->getOperationHours());
        $client->setHasMultipleWarehouses($request->getHasMultipleWarehouses() === 'si');
        $client->setAnnualSalesVolume($request->getAnnualSalesVolume());

        // Asociar el cliente con el usuario si es necesario
        $user = $this->userRepository->findOneBy(['uuid_user' => $request->getUuidUser()]);
        if ($user) {
            $client->addUser($user);
        } else {
            // Manejar el caso donde el usuario no existe
            throw new \Exception('Usuario no encontrado');
        }

        // Guardar el cliente en la base de datos
        $this->clientRepository->save($client);

        // Generar el token de verificación
        $this->generateVerificationToken($user);

        // Validar el usuario
        $this->validateUser($user);

        // Guardar el usuario y enviar notificaciones
        $this->saveUserAndSendNotifications($user);
        // Enviar el mensaje al bus de Messenger
        //$this->bus->dispatch(new CreateDockerContainerMessage($client->getUuidClient()));

        return $client;
    }



//    private function createClientContainer(Client $client, string $clientName, int $port): void
//    {
//        $scriptPath = '/appdata/www/bin/create_client_container.sh';
//        if (!file_exists($scriptPath)) {
//            throw new \Exception("Script not found: " . $scriptPath);
//        }
//
//        $command = sprintf(
//            'bash %s %s %d 2>&1',
//            escapeshellarg($scriptPath),
//            escapeshellarg($clientName),
//            $port
//        );
//        exec($command, $output, $return_var);
//
//        if ($return_var !== 0) {
//            error_log('Error creating client container: ' . implode("\n", $output));
//            throw new \Exception('Error creating client container: ' . implode("\n", $output));
//        }
//    }

    /**
     * @throws \DateMalformedStringException
     * @throws RandomException
     */
    private function generateVerificationToken(User $user): void
    {
        $verificationToken = bin2hex(random_bytes(32));
        $user->setVerificationToken($verificationToken);
        $user->setVerificationTokenExpiresAt((new \DateTime())->modify('+1 day'));
    }

    private function validateUser(User $user): void
    {
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            throw new ValidationException((string) $errors);
        }
    }

    /**
     * @throws \Exception
     */
    private function saveUserAndSendNotifications(User $user): void
    {
        try {
            $this->userRepository->save($user);

            $this->notificationService->sendEmailVerificationToUser($user);
            $this->notificationService->sendEmailToBack($user);

            //$this->entityManager->commit();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}