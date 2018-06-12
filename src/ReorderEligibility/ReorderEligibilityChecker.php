<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Component\Core\Model\OrderInterface;

interface ReorderEligibilityChecker
{
    /**
     * Returns array of arrays
     *
     * @param OrderInterface $order
     * @param OrderInterface $reorder
     * @return mixed
     */
    public function check(OrderInterface $order, OrderInterface $reorder);
}
