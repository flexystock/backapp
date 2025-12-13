<?php

namespace App\Order\Application\Services;

use App\Entity\Client\Order;
use App\Entity\Client\OrderHistory;
use App\Entity\Client\OrderItem;
use App\Entity\Client\Product;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class AutoOrderService
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Create an automatic order for a product when stock is below minimum
     *
     * @param EntityManagerInterface $entityManager Client's entity manager (multi-tenant)
     * @param Product $product Product that needs restocking
     * @param float $currentWeight Current weight from TTN
     * @param float $minimumStock Minimum stock threshold
     * @return Order|null Created order or null if conditions not met
     */
    public function createAutoOrder(
        EntityManagerInterface $entityManager,
        Product $product,
        float $currentWeight,
        float $minimumStock
    ): ?Order {
        // Check if auto-order is enabled for this product
        if (!$product->isAutoOrderEnabled()) {
            $this->logger->info('[AutoOrder] Auto-order disabled for product', [
                'productId' => $product->getId(),
                'productName' => $product->getName(),
            ]);
            return null;
        }

        // ✅ CORRECTO: Usar getRepository del EntityManager
        $productSupplierRepo = $entityManager->getRepository(\App\Entity\Client\ProductSupplier::class);

        // Find preferred supplier
        $productSupplier = $productSupplierRepo->findOneBy([
            'productId' => $product->getId(),
            'isPreferred' => true
        ]);

        if (!$productSupplier) {
            // Try to get any supplier
            $suppliers = $productSupplierRepo->findBy(['productId' => $product->getId()]);
            $productSupplier = !empty($suppliers) ? $suppliers[0] : null;
        }

        if (!$productSupplier) {
            $this->logger->warning('[AutoOrder] No supplier found for product', [
                'productId' => $product->getId(),
                'productName' => $product->getName(),
            ]);
            return null;
        }

        // ✅ VERIFICAR: ¿Ya existe pedido pendiente para este producto?
        if ($this->hasPendingOrder($entityManager, $product->getId())) {
            $this->logger->info('[AutoOrder] Pending order already exists for product', [
                'productId' => $product->getId(),
            ]);
            return null;
        }

        // Calculate order quantity
        $quantityDays = $product->getAutoOrderQuantityDays();
        $deficit = $minimumStock - $currentWeight;
        $minOrderQty = $productSupplier->getMinOrderQuantity() ?? $deficit;
        $orderQuantity = max($deficit, $minOrderQty);

        // Generate unique order number
        $orderNumber = $this->generateOrderNumber($entityManager);

        // Create order
        $order = new Order();
        $order->setOrderNumber($orderNumber);
        $order->setClientSupplierId($productSupplier->getClientSupplierId());
        $order->setStatus('pending');
        $order->setCurrency('EUR');
        $order->setNotes(sprintf(
            '🤖 Pedido automático generado por stock crítico. Peso actual: %.2f kg, Stock mínimo: %.2f kg',
            $currentWeight,
            $minimumStock
        ));

        // Calculate delivery date
        $deliveryDays = $productSupplier->getDeliveryDays() ?? 2;
        $deliveryDate = new \DateTime();
        $deliveryDate->modify("+{$deliveryDays} days");
        $order->setDeliveryDate($deliveryDate);

        // Create order item
        $orderItem = new OrderItem();
        $orderItem->setProductId($product->getId());
        $orderItem->setQuantity($orderQuantity);
        $orderItem->setUnit('kg');
        $orderItem->setUnitPrice($productSupplier->getUnitPrice());
        $orderItem->setSubtotal($orderQuantity * ($productSupplier->getUnitPrice() ?? 0));
        $orderItem->setNotes(sprintf(
            'Cantidad calculada para %d días. Umbral: %s',
            $quantityDays,
            $product->getAutoOrderThreshold()
        ));

        // Set prediction data
        $orderItem->setPredictionData([
            'current_weight' => $currentWeight,
            'minimum_stock' => $minimumStock,
            'deficit' => $deficit,
            'quantity_days' => $quantityDays,
            'threshold' => $product->getAutoOrderThreshold(),
            'auto_generated' => true,
            'generated_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        // Update order total
        $order->setTotalAmount($orderItem->getSubtotal() ?? 0);

        // ✅ Persist order first
        $entityManager->persist($order);
        $entityManager->flush();

        // Set order_id and persist item
        $orderItem->setOrderId($order->getId());
        $entityManager->persist($orderItem);
        $entityManager->flush();

        // Create order history entry
        $orderHistory = new OrderHistory();
        $orderHistory->setOrderId($order->getId());
        $orderHistory->setStatusFrom(null);
        $orderHistory->setStatusTo('pending');
        $orderHistory->setNotes('Pedido automático creado por stock bajo detectado desde TTN');

        $entityManager->persist($orderHistory);
        $entityManager->flush();

        $this->logger->info('[AutoOrder] Automatic order created successfully', [
            'orderId' => $order->getId(),
            'orderNumber' => $order->getOrderNumber(),
            'productId' => $product->getId(),
            'productName' => $product->getName(),
            'quantity' => $orderQuantity,
            'supplierId' => $productSupplier->getClientSupplierId(),
            'totalAmount' => $order->getTotalAmount(),
        ]);

        return $order;
    }

    /**
     * Check if there's already a pending or sent order for this product
     */
    private function hasPendingOrder(EntityManagerInterface $em, int $productId): bool
    {
        $qb = $em->createQueryBuilder();
        $qb->select('COUNT(o.id)')
            ->from(\App\Entity\Client\Order::class, 'o')
            ->join(\App\Entity\Client\OrderItem::class, 'oi', 'WITH', 'oi.orderId = o.id')
            ->where('oi.productId = :productId')
            ->andWhere('o.status IN (:statuses)')
            ->setParameter('productId', $productId)
            ->setParameter('statuses', ['pending', 'sent']);

        return (int)$qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Generate a unique order number
     */
    private function generateOrderNumber(EntityManagerInterface $entityManager): string
    {
        $date = new \DateTime();
        $prefix = 'ORD-' . $date->format('Ymd');

        $connection = $entityManager->getConnection();
        $sql = "SELECT order_number FROM orders WHERE order_number LIKE :prefix ORDER BY order_number DESC LIMIT 1";
        $stmt = $connection->prepare($sql);
        $stmt->bindValue('prefix', $prefix . '%');
        $result = $stmt->executeQuery();
        $lastOrder = $result->fetchAssociative();

        if ($lastOrder) {
            $lastNumber = $lastOrder['order_number'];
            $sequence = (int) substr($lastNumber, -4);
            $newSequence = $sequence + 1;
        } else {
            $newSequence = 1;
        }

        return $prefix . '-' . str_pad((string)$newSequence, 4, '0', STR_PAD_LEFT);
    }
}