<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Reorder;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PromotionInterface;

final class OrdersComparator implements OrdersComparatorInterface
{
    public function hasAnyPromotionChanged(OrderInterface $firstOrder, OrderInterface $secondOrder): bool
    {
        if ($firstOrder->getPromotions()->count() !== $secondOrder->getPromotions()->count()) {
            return true;
        }

        /** @var PromotionInterface $promotion */
        foreach ($firstOrder->getPromotions() as $promotion) {
            if (!$secondOrder->getPromotions()->contains($promotion)) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyVariantPriceChanged(OrderInterface $firstOrder, OrderInterface $secondOrder): bool
    {
        $orderVariantNamesToTotal = [];
        $reorderVariantNamesToTotal = [];

        /** @var OrderItemInterface $orderItem */
        foreach ($firstOrder->getItems()->getValues() as $orderItem) {
            $orderVariantNamesToTotal[$orderItem->getVariantName()] = $orderItem->getUnitPrice();
        }

        /** @var OrderItemInterface $orderItem */
        foreach ($secondOrder->getItems()->getValues() as $reorderItem) {
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
