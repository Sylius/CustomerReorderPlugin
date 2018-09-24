<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Checker;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface OrderCustomerRelationCheckerInterface
{
    public function wasOrderPlacedByCustomer(OrderInterface $order, CustomerInterface $customer): bool;
}
