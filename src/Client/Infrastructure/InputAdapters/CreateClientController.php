<?php
declare(strict_types=1);

namespace App\Client\Infrastructure\InputAdapters;

use App\Client\Infrastructure\InputPorts\CreateClientInputPort;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateClientController
{
    private CreateClientInputPort $createInputPort;

    public function __construct(CreateClientInputPort $createInputPort)
    {
        $this->createInputPort = $createInputPort;
    }
    #[Route('/api/client/create', name: 'create_client', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        try {
            $client = $this->createInputPort->create($data);
            return new Response('Client registered successfully', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}