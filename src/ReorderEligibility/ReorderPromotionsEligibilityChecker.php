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

    public function check(OrderInterface $order, OrderInterface $reorder): ReorderEligibilityCheckerResponse
    {
        $response = new ReorderEligibilityCheckerResponse();

        if (empty($reorder->getItems()->getValues()) ||
            $order->getPromotions()->getValues() === $reorder->getPromotions()->getValues()
        ) {
            $response->addResults([ReorderPromotionsEligibilityChecker::class => true]);
            return $response;
        }

        $disabledPromotions = [];

        /** @var PromotionInterface $promotion */
        foreach ($order->getPromotions()->getValues() as $promotion) {
            if (!in_array($promotion, $reorder->getPromotions()->getValues(), true)) {
                array_push($disabledPromotions, $promotion->getName());
            }
        }

        $response->addResults([ReorderPromotionsEligibilityChecker::class => false]);
        $response->addMessages([
            ReorderPromotionsEligibilityChecker::class => $this->reorderEligibilityConstraintMessageFormatter->format($disabledPromotions)
        ]);

        return $response;
    }
}
