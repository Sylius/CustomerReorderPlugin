<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Checker;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderCustomerRelationChecker implements OrderCustomerRelationCheckerInterface
{
    public function wasOrderPlacedByCustomer(OrderInterface $order, CustomerInterface $customer): bool
    {
        return
            null !== $customer &&
            null !== $order->getCustomer() &&
            $order->getCustomer()->getId() === $customer->getId()
        ;
    }
}
