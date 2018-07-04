<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\EligibilityCheckerFailureResponses;

final class InsufficientItemQuantityEligibilityChecker implements ReorderEligibilityChecker
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
        $orderProductNamesToQuantity = [];
        $reorderProductNamesToQuantity = [];

        /** @var OrderItemInterface $item */
        foreach ($order->getItems()->getValues() as $item) {
            $orderProductNamesToQuantity[$item->getProductName()] = $item->getQuantity();
        }

        /** @var OrderItemInterface $item */
        foreach ($reorder->getItems()->getValues() as $item) {
            $reorderProductNamesToQuantity[$item->getProductName()] = $item->getQuantity();
        }

        $insufficientItems = [];

        /** @var OrderItemInterface $item */
        foreach (array_keys($orderProductNamesToQuantity) as $productName) {
            if (!array_key_exists($productName, $reorderProductNamesToQuantity)) {
                continue;
            }

            if ($orderProductNamesToQuantity[$productName] > $reorderProductNamesToQuantity[$productName]) {
                array_push($insufficientItems, $productName);
            }
        }

        if (empty($insufficientItems)) {
            return [];
        }

        $reorderEligibilityCheckerResponse = new ReorderEligibilityCheckerResponse();

        $reorderEligibilityCheckerResponse->setMessage(EligibilityCheckerFailureResponses::INSUFFICIENT_ITEM_QUANTITY);
        $reorderEligibilityCheckerResponse->setParameters([
            '%order_items%' => $this->reorderEligibilityConstraintMessageFormatter->format($insufficientItems),
        ]);

        return [$reorderEligibilityCheckerResponse];
    }
}
