<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Component\Core\Model\OrderInterface;

interface ReorderEligibilityChecker
{
    public function check(OrderInterface $order, OrderInterface $reorder): array;
}
