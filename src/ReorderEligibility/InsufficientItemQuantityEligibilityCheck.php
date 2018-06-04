<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class InsufficientItemQuantityEligibilityCheck implements ReorderEligibilityChecker
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
        $orderVariantNamesToQuantity = [];
        $reorderVariantNamesToQuantity = [];

        /** @var OrderItemInterface $item */
        foreach ($order->getItems()->getValues() as $item) {
            $orderVariantNamesToQuantity[$item->getVariantName()] = $item->getQuantity();
        }

        /** @var OrderItemInterface $item */
        foreach ($reorder->getItems()->getValues() as $item) {
            $reorderVariantNamesToQuantity[$item->getVariantName()] = $item->getQuantity();
        }

        $insufficientItems = [];

        /** @var OrderItemInterface $item */
        foreach (array_keys($orderVariantNamesToQuantity) as $variantName) {
            if (!array_key_exists($variantName, $reorderVariantNamesToQuantity)) {
                continue;
            }

            if ($orderVariantNamesToQuantity[$variantName] !== $reorderVariantNamesToQuantity[$variantName]) {
                array_push($insufficientItems, $variantName);
            }
        }

        if (empty($insufficientItems)) {
            return [];
        }

        return [
            'type' => 'info',
            'message' => 'sylius.reorder.items_price_changed',
            'parameters' => [
                '%order_items%' => $this->reorderEligibilityConstraintMessageFormatter->format($insufficientItems),
            ]
        ];
    }
}
