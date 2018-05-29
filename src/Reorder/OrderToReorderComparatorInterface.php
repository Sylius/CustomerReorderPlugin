<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Reorder;

use Sylius\Component\Core\Model\OrderInterface;

interface OrderToReorderComparatorInterface
{
    public function havePromotionsChanged(OrderInterface $order, OrderInterface $reorder): bool;
    public function haveItemsPricesChanged(OrderInterface $order, OrderInterface $reorder): bool;
}
