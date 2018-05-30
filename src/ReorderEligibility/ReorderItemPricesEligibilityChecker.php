<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class ReorderItemPricesEligibilityChecker implements ReorderEligibilityChecker
{
    public function check(OrderInterface $order, OrderInterface $reorder)
    {
        $orderVariantNamesToTotal = [];
        $reorderVariantNamesToTotal = [];

        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems()->getValues() as $orderItem) {
            $orderVariantNamesToTotal[$orderItem->getVariantName()] = $orderItem->getUnitPrice();
        }

        /** @var OrderItemInterface $reorderItem */
        foreach ($reorder->getItems()->getValues() as $reorderItem) {
            $reorderVariantNamesToTotal[$reorderItem->getVariantName()] = $reorderItem->getUnitPrice();
        }

        foreach (array_keys($orderVariantNamesToTotal) as $key) {
            if (!array_key_exists($key, $reorderVariantNamesToTotal)) {
                continue;
            }

            if ($orderVariantNamesToTotal[$key] !== $reorderVariantNamesToTotal[$key]) {
                return [
                    'type' => 'info',
                    'message' => 'sylius.reorder.items_price_changed'
                ];
            }
        }

        return [];
    }
}
