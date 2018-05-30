<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;

final class ReorderPromotionsEligibilityChecker implements ReorderEligibilityChecker
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
        if (empty($reorder->getItems()) ||
            $order->getPromotions()->getValues() === $reorder->getPromotions()->getValues()
        ) {
            return [];
        }

        $disabledPromotions = [];

        /** @var PromotionInterface $promotion */
        foreach ($order->getPromotions()->getValues() as $promotion) {
            if (!in_array($promotion, $reorder->getPromotions()->getValues())) {
                array_push($disabledPromotions, $promotion->getName());
            }
        }

        return [
            'type' => 'info',
            'message' => 'sylius.reorder.promotion_not_enabled',
            'parameters' => [
                '%promotions%' => $this->reorderEligibilityConstraintMessageFormatter->format($disabledPromotions),
            ]
        ];
    }
}
