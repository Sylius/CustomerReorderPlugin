<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\CustomerReorderPlugin\Factory\OrderFactory;
use Sylius\CustomerReorderPlugin\Factory\OrderFactoryInterface;

final class OrderFactorySpec extends ObjectBehavior
{
    function let(
        FactoryInterface $decoratedFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        FactoryInterface $orderItemFactory
    ) {
        $this->beConstructedWith($decoratedFactory, $orderItemQuantityModifier, $orderItemFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderFactory::class);
    }

    function it_implements_order_factory_interface()
    {
        $this->shouldImplement(OrderFactoryInterface::class);
    }

    function it_creates_new_order(FactoryInterface $decoratedFactory, OrderInterface $order)
    {
        $decoratedFactory->createNew()->willReturn($order);

        $this->createNew()->shouldReturnAnInstanceOf(OrderInterface::class);
    }

    function it_creates_reorder_from_existing_order(
        FactoryInterface $decoratedFactory,
        OrderInterface $order,
        OrderInterface $reorder,
        ChannelInterface $channel,
        CustomerInterface $customer
    ) {
        $decoratedFactory->createNew()->willReturn($reorder);
        $order->getCustomer()->willReturn($customer);
        $order->getCurrencyCode()->willReturn('USD');
        $order->getCheckoutState()->willReturn(OrderCheckoutStates::STATE_CART);
        $order->getLocaleCode()->willReturn('en_US');
        $order->getNotes()->willReturn('');
        $order->getPaymentState()->willReturn(PaymentInterface::STATE_CART);
        $order->getItems()->willReturn(new ArrayCollection());

        $reorder->setChannel($channel)->shouldBeCalled();
        $reorder->setCustomer($customer)->shouldBeCalled();
        $reorder->setCurrencyCode('USD')->shouldBeCalled();
        $reorder->setCheckoutState(OrderCheckoutStates::STATE_CART)->shouldBeCalled();
        $reorder->setLocaleCode('en_US')->shouldBeCalled();
        $reorder->setNotes('')->shouldBeCalled();
        $reorder->setPaymentState(PaymentInterface::STATE_CART)->shouldBeCalled();

        $this->createFromExistingOrder($order, $channel);
    }
}
