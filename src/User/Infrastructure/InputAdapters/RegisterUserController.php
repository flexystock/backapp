<?php
declare(strict_types=1);
namespace App\User\Infrastructure\InputAdapters;

use App\User\Application\RegisterUserUseCase;
use App\User\Infrastructure\InputPorts\RegisterUserInputPort;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterUserController
{
    private $registerUseCase;

    public function __construct(RegisterUserUseCase $registerUseCase)
    {
        $this->registerUseCase = $registerUseCase;
    }

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request): Response
    {

        $data = json_decode($request->getContent(), true);
        try {
            $user = $this->registerUseCase->register($data);
            return new Response('User registered successfully', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}