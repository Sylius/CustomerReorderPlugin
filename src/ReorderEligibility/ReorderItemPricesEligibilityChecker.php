<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class ReorderItemPricesEligibilityChecker implements ReorderEligibilityChecker
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

        $orderVariantsWithChangedPrice = [];

        foreach (array_keys($orderVariantNamesToTotal) as $variantName) {
            if (!array_key_exists($variantName, $reorderVariantNamesToTotal)) {
                continue;
            }

            if ($orderVariantNamesToTotal[$variantName] !== $reorderVariantNamesToTotal[$variantName]) {
                array_push($orderVariantsWithChangedPrice, $variantName);
            }
        }

        if (empty($orderVariantsWithChangedPrice)) {
            return [];
        }

        return [
            'type' => 'info',
            'message' => 'sylius.reorder.items_price_changed',
            'parameters' => [
                '%order_items%' => $this->reorderEligibilityConstraintMessageFormatter->format($orderVariantsWithChangedPrice),
            ]
        ];
    }
}
