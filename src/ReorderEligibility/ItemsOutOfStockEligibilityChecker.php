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

        $messageParameter = '';

        if (count($variantsOutOfStock) === 1) {
            $messageParameter = array_pop($variantsOutOfStock);
        }

        else {
            $lastItemOutOfStock = end($variantOutOfStock);
            foreach ($variantsOutOfStock as $variantOutOfStock) {
                $messageParameter .= $variantOutOfStock . ($variantOutOfStock !== $lastItemOutOfStock) ? ', ' : '';
            }
        }

        return [
            'type' => 'info',
            'message' => 'sylius.reorder.items_out_of_stock',
            'parameters' => [
                '%order_items%' => $messageParameter
            ]
        ];
    }
}
