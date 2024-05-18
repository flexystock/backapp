<?php
declare(strict_types=1);
namespace App\User\Infrastructure\InputAdapters;

use App\User\Infrastructure\InputPorts\LoginInputPort;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\User\Infrastructure\OutputPorts\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\DatabaseConnectionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends AbstractController
{
    private $loginInputPort;
    private $dbManager;

    public function __construct(LoginInputPort $loginInputPort, DatabaseConnectionManager $dbManager)
    {
        $this->loginInputPort = $loginInputPort;
        $this->dbManager = $dbManager;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): Response
    {
        die("llegamos");
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        // Obtener el administrador de entidades principal
        $em = $this->dbManager->getDefaultEntityManager();

        // Verificar el usuario en la base de datos principal
        $userRepo = $em->getRepository('App\Main\Entity\User');
        $user = $userRepo->findOneBy(['email' => $email]);

        if (!$user || !password_verify($password, $user->getPassword())) {
            return new Response('Invalid credentials', Response::HTTP_UNAUTHORIZED);
        }

        // Obtener el UUID del cliente del usuario
        $profileRepo = $em->getRepository('App\Main\Entity\ProfileUserClient');
        $profile = $profileRepo->findOneBy(['uuidUser' => $user->getUuid()]);

        if (!$profile) {
            return new Response('No client associated with this user', Response::HTTP_UNAUTHORIZED);
        }

        $uuidClient = $profile->getUuidClient();

        // Generar el token JWT
        $token = $this->loginInputPort->generateToken($user->getUuid(), $uuidClient);

        return $this->json(['token' => $token]);
    }
}





