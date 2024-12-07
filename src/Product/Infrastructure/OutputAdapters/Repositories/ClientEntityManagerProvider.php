<?php

namespace App\Product\Infrastructure\OutputAdapters\Repositories;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ClientEntityManagerProvider
{
    private RequestStack $requestStack;
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request || !$request->attributes->has('clientEm')) {
            throw new \RuntimeException('No client EntityManager found in request attributes.');
        }

        return $request->attributes->get('clientEm');
    }
}
