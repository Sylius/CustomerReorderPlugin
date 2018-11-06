<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\CustomerReorderPlugin\Checker\OrderCustomerRelationCheckerInterface;

final class OrderCustomerRelationCheckerSpec extends ObjectBehavior
{
    function it_implements_order_customer_relation_checker_interface(): void
    {
        $this->shouldImplement(OrderCustomerRelationCheckerInterface::class);
    }

    function it_returns_true_when_order_was_placed_by_customer(
       CustomerInterface $orderCustomer,
       CustomerInterface $customer,
       OrderInterface $order
   ): void {
        $orderCustomer->getId()->willReturn(1);
        $customer->getId()->willReturn(1);

        $order->getCustomer()->willReturn($orderCustomer);

        $this->wasOrderPlacedByCustomer($order, $customer)->shouldReturn(true);
    }

    function it_returns_false_when_order_was_not_placed_by_customer(
       CustomerInterface $orderCustomer,
       CustomerInterface $customer,
       OrderInterface $order
   ): void {
        $orderCustomer->getId()->willReturn(1);
        $customer->getId()->willReturn(2);

        $order->getCustomer()->willReturn($orderCustomer);

        $this->wasOrderPlacedByCustomer($order, $customer)->shouldReturn(false);
    }

    function it_returns_false_when_order_has_no_customer_assigned(
       CustomerInterface $customer,
       OrderInterface $order
   ): void {
        $order->getCustomer()->willReturn(null);

        $this->wasOrderPlacedByCustomer($order, $customer)->shouldReturn(false);
    }
}
