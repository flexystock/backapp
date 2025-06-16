<?php

declare(strict_types=1);

namespace App\Admin\Infrastructure\InputAdapters;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/api/admin', name: 'admin_dashboard', methods: ['GET'])]
    public function dashboard(): JsonResponse
    {
        return new JsonResponse(['message' => 'Admin dashboard']);
    }
}
