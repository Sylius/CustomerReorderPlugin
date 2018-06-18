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

            if ($orderVariantNamesToQuantity[$variantName] > $reorderVariantNamesToQuantity[$variantName]) {
                array_push($insufficientItems, $variantName);
            }
        }

        if (empty($insufficientItems)) {
            return [];
        }

        $reorderEligibilityCheckerResponse = new ReorderEligibilityCheckerResponse();

        $reorderEligibilityCheckerResponse->setMessage(EligibilityCheckerFailureResponses::INSUFFICIENT_ITEM_QUANTITY);
        $reorderEligibilityCheckerResponse->setParameters([
            '%order_items%' => $this->reorderEligibilityConstraintMessageFormatter->format($insufficientItems)
        ]);

        return [$reorderEligibilityCheckerResponse];
    }
}
