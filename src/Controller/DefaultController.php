<?php
declare(strict_types =1);
// src/Controller/DefaultController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{

    #[Route('hola', name: 'hola', methods: ['GET','POST'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['message' => 'Has llamado correctamente al metodo de Back (symfony)']);
    }
}