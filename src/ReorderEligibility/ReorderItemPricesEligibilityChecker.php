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
        $orderProductNamesToTotal = [];
        $reorderProductNamesToTotal = [];

        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems()->getValues() as $orderItem) {
            $orderProductNamesToTotal[$orderItem->getProductName()] = $orderItem->getUnitPrice();
        }

        /** @var OrderItemInterface $reorderItem */
        foreach ($reorder->getItems()->getValues() as $reorderItem) {
            $reorderProductNamesToTotal[$reorderItem->getProductName()] = $reorderItem->getUnitPrice();
        }

        $orderItemsWithChangedPrice = [];

        foreach (array_keys($orderProductNamesToTotal) as $productName) {
            if (!array_key_exists($productName, $reorderProductNamesToTotal)) {
                continue;
            }

            if ($orderProductNamesToTotal[$productName] !== $reorderProductNamesToTotal[$productName]) {
                array_push($orderItemsWithChangedPrice, $productName);
            }
        }

        if (empty($orderItemsWithChangedPrice)) {
            return [];
        }

        $eligibilityCheckerResponse = new ReorderEligibilityCheckerResponse();

        $eligibilityCheckerResponse->setMessage(EligibilityCheckerFailureResponses::REORDER_ITEMS_PRICES_CHANGED);
        $eligibilityCheckerResponse->setParameters([
            '%product_names%' => $this->reorderEligibilityConstraintMessageFormatter->format($orderItemsWithChangedPrice),
        ]);

        return [$eligibilityCheckerResponse];
    }
}
