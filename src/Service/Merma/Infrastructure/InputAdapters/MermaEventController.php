<?php

namespace App\Service\Merma\Infrastructure\InputAdapters;

use App\Service\Merma\Application\InputPorts\MermaDashboardInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * MermaEventController
 *
 * Gestiona las acciones del usuario sobre los eventos de merma:
 *   - Confirmar una anomalía como sustracción real
 *   - Descartar una anomalía (accidente, error operativo)
 *
 * El widget del dashboard (GET) se renderiza desde el ProductDashboardController
 * existente — no hace falta un endpoint propio para eso.
 */
#[Route('/merma/event', name: 'merma_event_')]
final class MermaEventController extends AbstractController
{
    public function __construct(
        private readonly MermaDashboardInterface $dashboard,
    ) {}

    /**
     * Confirmar anomalía como sustracción real.
     * Llamado desde los botones del widget _widget.html.twig
     */
    #[Route('/{id}/confirm', name: 'confirm', methods: ['POST'])]
    public function confirm(int $id): Response
    {
        try {
            $this->dashboard->confirmAnomaly($id);
            $this->addFlash('success', 'Anomalía confirmada como sustracción.');
        } catch (\InvalidArgumentException $e) {
            $this->addFlash('error', 'Evento no encontrado.');
        }

        return $this->redirectToReferer();
    }

    /**
     * Descartar anomalía (no fue una sustracción).
     */
    #[Route('/{id}/discard', name: 'discard', methods: ['POST'])]
    public function discard(int $id): Response
    {
        try {
            $this->dashboard->discardAnomaly($id);
            $this->addFlash('info', 'Anomalía descartada.');
        } catch (\InvalidArgumentException $e) {
            $this->addFlash('error', 'Evento no encontrado.');
        }

        return $this->redirectToReferer();
    }

    /**
     * Endpoint JSON para el widget si lo cargas por AJAX (opcional).
     */
    #[Route('/summary/{scaleId}/{productId}', name: 'summary', methods: ['GET'])]
    public function summary(int $scaleId, int $productId): JsonResponse
    {
        $summary = $this->dashboard->getSummary($scaleId, $productId);

        return $this->json([
            'input_kg'              => $summary->inputKg,
            'consumed_kg'           => $summary->consumedKg,
            'anomaly_kg'            => $summary->anomalyKg,
            'estimated_waste_kg'    => $summary->estimatedWasteKg,
            'estimated_waste_pct'   => $summary->estimatedWastePct,
            'estimated_cost_euros'  => $summary->estimatedCostEuros,
            'pending_anomalies'     => $summary->pendingAnomaliesCount,
            'status'                => $summary->getStatus(),
        ]);
    }

    private function redirectToReferer(): Response
    {
        $referer = $this->container->get('request_stack')->getCurrentRequest()?->headers->get('referer');
        return $referer
            ? $this->redirect($referer)
            : $this->redirectToRoute('dashboard_index');
    }
}