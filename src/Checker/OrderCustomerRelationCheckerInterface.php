<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Checker;

use Sylius\Component\Core\Model\OrderInterface;

interface OrderCustomerRelationCheckerInterface
{
    public function wasOrderPlacedByCurrentCustomer(OrderInterface $order): bool;
}
