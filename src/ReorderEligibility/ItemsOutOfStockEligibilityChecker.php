<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class ItemsOutOfStockEligibilityChecker implements ReorderEligibilityChecker
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
        $isAnyItemOutOfStock = false;
        $variantsOutOfStock = [];

        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems()->getValues() as $orderItem) {
            if (!($orderItem->getVariant()->isTracked() && $orderItem->getVariant()->isInStock())) {
                $isAnyItemOutOfStock = true;
                array_push($variantsOutOfStock, $orderItem->getVariantName());
            }
        }

        if (!$isAnyItemOutOfStock) {
            return [];
        }

        return [
            'type' => 'info',
            'message' => 'sylius.reorder.items_out_of_stock',
            'parameters' => [
                '%order_items%' => $this->reorderEligibilityConstraintMessageFormatter->format($variantsOutOfStock),
            ]
        ];
    }
}
