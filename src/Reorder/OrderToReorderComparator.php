<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Reorder;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class OrderToReorderComparator implements OrderToReorderComparatorInterface
{
    public function havePromotionsChanged(OrderInterface $order, OrderInterface $reorder): bool
    {
        return $order->getPromotions()->getValues() != $reorder->getPromotions()->getValues();
    }

    public function haveItemsPricesChanged(OrderInterface $order, OrderInterface $reorder): bool
    {
        $orderVariantNamesToTotal = [];
        $reorderVariantNamesToTotal = [];

        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems()->getValues() as $orderItem) {
            $orderVariantNamesToTotal[$orderItem->getVariantName()] = $orderItem->getUnitPrice();
        }

        /** @var OrderItemInterface $orderItem */
        foreach ($reorder->getItems()->getValues() as $reorderItem) {
            $reorderVariantNamesToTotal[$reorderItem->getVariantName()] = $reorderItem->getUnitPrice();
        }

        foreach (array_keys($orderVariantNamesToTotal) as $key) {
            if ($orderVariantNamesToTotal[$key] !== $reorderVariantNamesToTotal[$key]) {
                return true;
            }
        }

        return false;
    }
}
