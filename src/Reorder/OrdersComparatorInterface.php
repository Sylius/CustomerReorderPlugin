<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Reorder;

use Sylius\Component\Core\Model\OrderInterface;

interface OrdersComparatorInterface
{
    public function hasAnyPromotionChanged(OrderInterface $firstOrder, OrderInterface $secondOrder): bool;
    public function hasAnyVariantPriceChanged(OrderInterface $firstOrder, OrderInterface $secondOrder): bool;
}
