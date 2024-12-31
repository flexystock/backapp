<?php

namespace App\Client\Application\UseCases;

use App\Client\Application\DTO\RegisterClientRequest;
use App\Client\Application\InputPorts\RegisterClientInputPort;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Client\Application\RandomException;
use App\Entity\Main\Client;
use App\Entity\Main\User;
use App\Service\DockerService;
use App\User\Application\OutputPorts\NotificationServiceInterface;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use Cassandra\Exception\ValidationException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        $client = new Client(); // uuiCLient y User_id

        $client->setName($request->getName());
        $client->setClientName($request->getName());
        $client->setBusinessType('HOSTELERIA');
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
        $client->setNumberWarehouses($request->getNumberWarehouses());
        $client->setAnnualSalesVolume($request->getAnnualSalesVolume());

        // Asociar el cliente con el usuario si es necesario
        $user = $this->userRepository->findOneBy(['uuid_user' => $request->getUuidUser()]);
        if ($user) {
            $user->addClient($client);
            $client->addUser($user);
        } else {
            // Manejar el caso donde el usuario no existe
            throw new \Exception('USER_NOT_FOUND');
        }
        // Guardar el cliente en la base de datos
        $this->clientRepository->save($client);
        // Generar el token de verificación
        $this->generateVerificationToken($user);
        // Validar el usuario
        $this->validateUser($user);
        // Guardar el usuario y enviar notificaciones
        $this->saveUserAndSendNotifications($user);

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

            // $this->entityManager->commit();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
