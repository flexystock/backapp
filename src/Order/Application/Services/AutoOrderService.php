<?php

namespace App\Order\Application\Services;

use App\Entity\Client\Order;
use App\Entity\Client\OrderHistory;
use App\Entity\Client\OrderItem;
use App\Entity\Client\Product;
use App\Order\Infrastructure\OutputAdapters\Repositories\OrderRepository;
use App\Order\Infrastructure\OutputAdapters\Repositories\OrderHistoryRepository;
use App\Order\Infrastructure\OutputAdapters\Repositories\OrderItemRepository;
use App\Order\Infrastructure\OutputAdapters\Repositories\ProductSupplierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Service to create automatic orders when stock is low
 */
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
     * @param EntityManagerInterface $entityManager Client's entity manager
     * @param Product $product Product that needs restocking
     * @param float $currentWeight Current weight from TTN
     * @param float $minimumStock Minimum stock threshold
     * @return Order|null Created order or null if auto-order is disabled or no supplier found
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

        // Find preferred supplier for this product
        $productSupplierRepo = new ProductSupplierRepository($entityManager);
        $productSupplier = $productSupplierRepo->findPreferredByProductId($product->getId());

        if (!$productSupplier) {
            // If no preferred supplier, try to get any supplier
            $suppliers = $productSupplierRepo->findByProductId($product->getId());
            $productSupplier = !empty($suppliers) ? $suppliers[0] : null;
        }

        if (!$productSupplier) {
            $this->logger->warning('[AutoOrder] No supplier found for product', [
                'productId' => $product->getId(),
                'productName' => $product->getName(),
            ]);
            return null;
        }

        // Calculate order quantity based on auto_order_quantity_days
        $quantityDays = $product->getAutoOrderQuantityDays();
        $deficit = $minimumStock - $currentWeight;
        
        // Use minimum order quantity from supplier or calculated deficit
        $minOrderQty = $productSupplier->getMinOrderQuantity() ?? $deficit;
        $orderQuantity = max($deficit, $minOrderQty);

        // Generate unique order number
        $orderNumber = $this->generateOrderNumber($entityManager);

        // Create order
        $order = new Order();
        $order->setOrderNumber($orderNumber);
        $order->setClientSupplierId($productSupplier->getClientSupplierId());
        $order->setStatus('pending'); // Set as pending for user confirmation
        $order->setCurrency('EUR');
        $order->setNotes(sprintf(
            'Pedido automático generado por stock bajo. Peso actual: %.2f, Stock mínimo: %.2f',
            $currentWeight,
            $minimumStock
        ));

        // Calculate delivery date based on supplier's delivery days
        $deliveryDays = $productSupplier->getDeliveryDays() ?? 2;
        $deliveryDate = new \DateTime();
        $deliveryDate->modify("+{$deliveryDays} days");
        $order->setDeliveryDate($deliveryDate);

        // Create order repository and save
        $orderRepo = new OrderRepository($entityManager);
        
        // Create order item
        $orderItem = new OrderItem();
        $orderItem->setProductId($product->getId());
        $orderItem->setQuantity($orderQuantity);
        $orderItem->setUnit('kg'); // Default unit
        $orderItem->setUnitPrice($productSupplier->getUnitPrice());
        $orderItem->setSubtotal($orderQuantity * ($productSupplier->getUnitPrice() ?? 0));
        $orderItem->setNotes(sprintf(
            'Cantidad calculada para %d días. Umbral: %s',
            $quantityDays,
            $product->getAutoOrderThreshold()
        ));

        // Set prediction data if available
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

        // Persist order first
        $orderRepo->save($order);

        // Now set the order_id on the item and persist
        $orderItem->setOrderId($order->getId());
        $orderItemRepo = new OrderItemRepository($entityManager);
        $orderItemRepo->save($orderItem);

        // Create order history entry
        $orderHistory = new OrderHistory();
        $orderHistory->setOrderId($order->getId());
        $orderHistory->setStatusFrom(null);
        $orderHistory->setStatusTo('pending');
        $orderHistory->setNotes('Pedido automático creado por stock bajo detectado desde TTN');

        $orderHistoryRepo = new OrderHistoryRepository($entityManager);
        $orderHistoryRepo->save($orderHistory);

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
     * Generate a unique order number
     *
     * @param EntityManagerInterface $entityManager
     * @return string
     */
    private function generateOrderNumber(EntityManagerInterface $entityManager): string
    {
        $date = new \DateTime();
        $prefix = 'ORD-' . $date->format('Ymd');
        
        // Find the last order number for today
        $connection = $entityManager->getConnection();
        
        $sql = "SELECT order_number FROM orders WHERE order_number LIKE :prefix ORDER BY order_number DESC LIMIT 1";
        $stmt = $connection->prepare($sql);
        $stmt->bindValue('prefix', $prefix . '%');
        $result = $stmt->executeQuery();
        $lastOrder = $result->fetchAssociative();
        
        if ($lastOrder) {
            // Extract sequence number and increment
            $lastNumber = $lastOrder['order_number'];
            $sequence = (int) substr($lastNumber, -4);
            $newSequence = $sequence + 1;
        } else {
            $newSequence = 1;
        }
        
        return $prefix . '-' . str_pad((string)$newSequence, 4, '0', STR_PAD_LEFT);
    }
}
