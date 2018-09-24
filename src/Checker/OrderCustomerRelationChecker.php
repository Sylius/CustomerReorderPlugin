<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Checker;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderCustomerRelationChecker implements OrderCustomerRelationCheckerInterface
{
    public function wasOrderPlacedByCustomer(OrderInterface $order, CustomerInterface $customer): bool
    {
        /** @var CustomerInterface $orderCustomer */
        $orderCustomer = $order->getCustomer();

        return
            null != $customer &&
            null != $orderCustomer &&
            $orderCustomer->getId() === $customer->getId()
        ;
    }
}
