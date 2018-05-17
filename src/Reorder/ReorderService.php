<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Reorder;

use Sylius\Component\Core\Model\OrderInterface;

final class ReorderService implements ReorderServiceInterface
{
    public function reorder(OrderInterface $order): OrderInterface
    {
        $itemUtils = $order->getItemUnits();

        return null;
    }
}
