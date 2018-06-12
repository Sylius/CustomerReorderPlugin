<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ItemsOutOfStockEligibilityChecker implements ReorderEligibilityChecker
{
    /** @var ReorderEligibilityConstraintMessageFormatterInterface */
    private $reorderEligibilityConstraintMessageFormatter;

    public function __construct(
        ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter
    ) {
        $this->reorderEligibilityConstraintMessageFormatter = $reorderEligibilityConstraintMessageFormatter;
    }

    public function check(OrderInterface $order, OrderInterface $reorder)
    {
        $variantsOutOfStock = [];

        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems()->getValues() as $orderItem) {
            /** @var ProductVariantInterface $productVariant */
            $productVariant = $orderItem->getVariant();
            if (!($productVariant->isTracked() && $productVariant->isInStock())) {
                array_push($variantsOutOfStock, $orderItem->getVariantName());
            }
        }

        if (empty($variantsOutOfStock)) {
            return [];
        }

        return [
            'type' => 'info',
            'message' => 'sylius.reorder.items_out_of_stock',
            'parameters' => [
                '%order_items%' => $this->reorderEligibilityConstraintMessageFormatter->format($variantsOutOfStock),
            ]
        ];
    }
}
