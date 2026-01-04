<?php

declare(strict_types=1);

namespace App\ControlPanel\Purchase\Application\UseCases;

use App\ControlPanel\Purchase\Application\DTO\GetPurchaseScalesRequest;
use App\ControlPanel\Purchase\Application\DTO\GetPurchaseScalesResponse;
use App\ControlPanel\Purchase\Application\InputPorts\GetPurchaseScalesUseCaseInterface;
use App\ControlPanel\Purchase\Application\OutputPorts\PurchaseScalesRepositoryInterface;
use App\Entity\Main\PurchaseScales;

class GetPurchaseScalesUseCase implements GetPurchaseScalesUseCaseInterface
{
    private PurchaseScalesRepositoryInterface $purchaseScalesRepository;

    public function __construct(PurchaseScalesRepositoryInterface $purchaseScalesRepository)
    {
        $this->purchaseScalesRepository = $purchaseScalesRepository;
    }

    public function execute(GetPurchaseScalesRequest $request): GetPurchaseScalesResponse
    {
        // If specific purchase is requested by UUID
        if ($request->getUuidPurchase() !== null) {
            $purchase = $this->purchaseScalesRepository->findByUuidPurchase($request->getUuidPurchase());
            $purchases = $purchase !== null ? [$purchase] : [];
        }
        // If filtering by client UUID
        elseif ($request->getUuidClient() !== null) {
            $purchases = $this->purchaseScalesRepository->findByUuidClient($request->getUuidClient());
        }
        // If filtering by status
        elseif ($request->getStatus() !== null) {
            $purchases = $this->purchaseScalesRepository->findByStatus($request->getStatus());
        }
        // Otherwise, get all purchases
        else {
            $purchases = $this->purchaseScalesRepository->findAll();
        }

        // Map purchases to array format
        $purchasesArray = array_map(
            fn(PurchaseScales $purchase) => $this->mapPurchaseToArray($purchase),
            $purchases
        );

        return new GetPurchaseScalesResponse($purchasesArray);
    }

    private function mapPurchaseToArray(PurchaseScales $purchase): array
    {
        return [
            'uuid_purchase' => $purchase->getUuidPurchase(),
            'uuid_client' => $purchase->getUuidClient(),
            'client_name' => $purchase->getClientName(),
            'quantity' => $purchase->getQuantity(),
            'status' => $purchase->getStatus(),
            'notes' => $purchase->getNotes(),
            'purchase_at' => $purchase->getPurchaseAt()->format('Y-m-d H:i:s'),
            'processed_at' => $purchase->getProcessedAt()?->format('Y-m-d H:i:s'),
            'processed_by_uuid_user' => $purchase->getProcessedByUuidUser(),
        ];
    }
}
