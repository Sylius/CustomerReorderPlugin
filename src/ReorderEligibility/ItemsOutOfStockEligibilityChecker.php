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

    public function check(OrderInterface $order, OrderInterface $reorder): ReorderEligibilityCheckerResponse
    {
        $variantsOutOfStock = [];

        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems()->getValues() as $orderItem) {
            /** @var ProductVariantInterface $productVariant */
            $productVariant = $orderItem->getVariant();
            if ($productVariant->isTracked() && !$productVariant->isInStock()) {
                $variantsOutOfStock[] = $orderItem->getVariantName();
            }
        }

        $response = new ReorderEligibilityCheckerResponse();

        if (empty($variantsOutOfStock)) {
            $response->addResults([ItemsOutOfStockEligibilityChecker::class => true]);
            return $response;
        }

        $response->addResults([ItemsOutOfStockEligibilityChecker::class => false]);
        $response->addMessages([
            ItemsOutOfStockEligibilityChecker::class => $this->reorderEligibilityConstraintMessageFormatter->format($variantsOutOfStock)
        ]);

        return $response;
    }
}
