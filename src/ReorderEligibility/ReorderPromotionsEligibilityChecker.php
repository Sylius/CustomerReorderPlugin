<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Component\Core\Model\OrderInterface;

final class ReorderPromotionsEligibilityChecker implements ReorderEligibilityChecker
{
    public function check(OrderInterface $order, OrderInterface $reorder)
    {
        if (!empty($reorder->getItems()->getValues()) &&
            $order->getPromotions()->getValues() != $reorder->getPromotions()->getValues()) {
            return [
                'type' => 'info',
                'message' => 'sylius.reorder.promotion_not_enabled'
            ];
        }

        return [];
    }
}
