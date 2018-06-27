<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\EligibilityCheckerFailureResponses;

final class ItemsOutOfStockEligibilityChecker implements ReorderEligibilityChecker
{
    /** @var ReorderEligibilityConstraintMessageFormatterInterface */
    private $reorderEligibilityConstraintMessageFormatter;

    public function __construct(
        ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter
    ) {
        $this->reorderEligibilityConstraintMessageFormatter = $reorderEligibilityConstraintMessageFormatter;
    }

    public function check(OrderInterface $order, OrderInterface $reorder): array
    {
        $productsOutOfStock = [];

        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems()->getValues() as $orderItem) {
            if (null === $orderItem->getVariant()) {
                continue;
            }

            /** @var ProductVariantInterface $productVariant */
            $productVariant = $orderItem->getVariant();
            if ($productVariant->isTracked() && !$productVariant->isInStock()) {
                $productsOutOfStock[] = $orderItem->getProductName();
            }
        }

        if (empty($productsOutOfStock)) {
            return [];
        }

        $eligibilityCheckerResponse = new ReorderEligibilityCheckerResponse();

        $eligibilityCheckerResponse->setMessage(EligibilityCheckerFailureResponses::ITEMS_OUT_OF_STOCK);
        $eligibilityCheckerResponse->setParameters([
            '%order_items%' => $this->reorderEligibilityConstraintMessageFormatter->format($productsOutOfStock)
        ]);

        return [$eligibilityCheckerResponse];
    }
}
