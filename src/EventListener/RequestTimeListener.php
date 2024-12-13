<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestTimeListener
{
    private \Symfony\Component\HttpFoundation\Request $masterRequest;

    public function onKernelRequest(RequestEvent $event): void
    {
        $this->masterRequest = $event->getRequest();
        $request = $event->getRequest();
        // Guardar el tiempo de inicio
        $request->attributes->set('_start_time', microtime(true));
    }
}
