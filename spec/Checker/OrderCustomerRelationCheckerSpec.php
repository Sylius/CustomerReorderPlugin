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
       CustomerInterface $customer,
       OrderInterface $order
   ): void {
        $customer->getId()->willReturn('1');

        $order->getCustomer()->willReturn($customer);

        $this->wasOrderPlacedByCustomer($order, $customer)->shouldReturn(true);
   }

   function it_returns_false_when_order_was_not_placed_by_customer(
       CustomerInterface $firstCustomer,
       CustomerInterface $secondCustomer,
       OrderInterface $order
   ): void {
        $firstCustomer->getId()->willReturn('1');
        $secondCustomer->getId()->willReturn('2');

        $order->getCustomer()->willReturn($secondCustomer);

       $this->wasOrderPlacedByCustomer($order, $firstCustomer)->shouldReturn(false);
   }
}
