<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\EligibilityCheckerFailureResponses;

final class ReorderPromotionsEligibilityChecker implements ReorderEligibilityChecker
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
        if (empty($reorder->getItems()->getValues()) ||
            $order->getPromotions()->getValues() === $reorder->getPromotions()->getValues()
        ) {
            return [];
        }

        $disabledPromotions = [];

        /** @var PromotionInterface $promotion */
        foreach ($order->getPromotions()->getValues() as $promotion) {
            if (!in_array($promotion, $reorder->getPromotions()->getValues(), true)) {
                array_push($disabledPromotions, $promotion->getName());
            }
        }

        $eligibilityCheckerResponse = new ReorderEligibilityCheckerResponse();

        $eligibilityCheckerResponse->setMessage(EligibilityCheckerFailureResponses::REORDER_PROMOTIONS_CHANGED);
        $eligibilityCheckerResponse->setParameters([
            '%promotion_names%' => $this->reorderEligibilityConstraintMessageFormatter->format($disabledPromotions),
        ]);

        return [$eligibilityCheckerResponse];
    }
}
