<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\CustomerReorderPlugin\Checker\OrderCustomerRelationCheckerInterface;

final class OrderCustomerRelationCheckerSpec extends ObjectBehavior
{
   function let(CustomerContextInterface $customerContext): void
   {
       $this->beConstructedWith($customerContext);
   }

   function it_implements_order_customer_relation_checker_interface(): void
   {
       $this->shouldImplement(OrderCustomerRelationCheckerInterface::class);
   }

   function it_returns_true_when_order_was_placed_by_customer(
       CustomerContextInterface $customerContext,
       CustomerInterface $customer,
       OrderInterface $order
   ): void {
        $customer->getId()->willReturn('1');

        $customerContext->getCustomer()->willReturn($customer);
        $order->getCustomer()->willReturn($customer);

        $this->wasOrderPlacedByCurrentCustomer($order)->shouldReturn(true);
   }

   function it_returns_false_when_order_was_not_placed_by_customer(
       CustomerContextInterface $customerContext,
       CustomerInterface $firstCustomer,
       CustomerInterface $secondCustomer,
       OrderInterface $order
   ): void {
        $firstCustomer->getId()->willReturn('1');
        $secondCustomer->getId()->willReturn('2');

        $customerContext->getCustomer()->willReturn($firstCustomer);
        $order->getCustomer()->willReturn($secondCustomer);

       $this->wasOrderPlacedByCurrentCustomer($order)->shouldReturn(false);
   }
}
