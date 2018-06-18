<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\EligibilityCheckerFailureResponses;

final class ReorderItemPricesEligibilityChecker implements ReorderEligibilityChecker
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

        $eligibilityCheckerResponse = new ReorderEligibilityCheckerResponse();

        $eligibilityCheckerResponse->setMessage(EligibilityCheckerFailureResponses::REORDER_ITEMS_PRICES_CHANGED);
        $eligibilityCheckerResponse->setParameters([
            '%product_names%' => $this->reorderEligibilityConstraintMessageFormatter->format($orderVariantsWithChangedPrice)
        ]);

        return [$eligibilityCheckerResponse];
    }
}
