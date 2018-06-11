<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\CustomerReorderPlugin\Factory\OrderFactory;
use Sylius\CustomerReorderPlugin\Factory\OrderFactoryInterface;

final class OrderFactorySpec extends ObjectBehavior
{
    function let(
        FactoryInterface $decoratedFactory,
        FactoryInterface $orderItemFactory,
        OrderModifierInterface $orderModifier,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier
    ) {
        $this->beConstructedWith($decoratedFactory, $orderItemFactory, $orderModifier, $orderItemQuantityModifier);
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
        FactoryInterface $orderItemFactory,
        OrderInterface $order,
        OrderInterface $reorder,
        ChannelInterface $channel,
        CustomerInterface $customer,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        ProductVariantInterface $firstProductVariant,
        ProductVariantInterface $secondProductVariant,
        OrderItemInterface $firstNewOrderItem,
        OrderItemInterface $secondNewOrderItem,
        AddressInterface $shippingAddress,
        AddressInterface $billingAddress,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderModifierInterface $orderModifier
    ) {
        $decoratedFactory->createNew()->willReturn($reorder);
        $order->getCustomer()->willReturn($customer);
        $order->getCurrencyCode()->willReturn('USD');
        $order->getLocaleCode()->willReturn('en_US');
        $order->getNotes()->willReturn('test_notes');
        $order->getShippingAddress()->willReturn($shippingAddress);
        $order->getBillingAddress()->willReturn($billingAddress);

        $reorder->setChannel($channel)->shouldBeCalled();
        $reorder->setCustomer($customer)->shouldBeCalled();
        $reorder->setCurrencyCode('USD')->shouldBeCalled();
        $reorder->setLocaleCode('en_US')->shouldBeCalled();
        $reorder->setNotes('test_notes')->shouldBeCalled();
        $reorder->setBillingAddress($billingAddress)->shouldBeCalled();
        $reorder->setShippingAddress($shippingAddress)->shouldBeCalled();

        $order->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject()
        ]));

        $firstOrderItem->getUnitPrice()->willReturn(10);
        $firstOrderItem->getVariant()->willReturn($firstProductVariant);
        $firstOrderItem->getQuantity()->willReturn(1);
        $firstOrderItem->getProductName()->willReturn('test_product_name_01');
        $firstOrderItem->getVariantName()->willReturn('test_variant_name_01');

        $secondOrderItem->getUnitPrice()->willReturn(20);
        $secondOrderItem->getVariant()->willReturn($secondProductVariant);
        $secondOrderItem->getQuantity()->willReturn(2);
        $secondOrderItem->getProductName()->willReturn('test_product_name_02');
        $secondOrderItem->getVariantName()->willReturn('test_variant_name_02');

        $orderItemFactory->createNew()->willReturn($firstNewOrderItem, $secondNewOrderItem);

        $firstProductVariant->isTracked()->willReturn(true);
        $firstProductVariant->isInStock()->willReturn(true);

        $secondProductVariant->isTracked()->willReturn(true);
        $secondProductVariant->isInStock()->willReturn(true);

        $firstNewOrderItem->setVariant($firstProductVariant)->shouldBeCalled();
        $firstNewOrderItem->setUnitPrice(10)->shouldBeCalled();
        $firstNewOrderItem->setProductName('test_product_name_01')->shouldBeCalled();
        $firstNewOrderItem->setVariantName('test_variant_name_01')->shouldBeCalled();

        $secondNewOrderItem->setVariant($secondProductVariant)->shouldBeCalled();
        $secondNewOrderItem->setUnitPrice(20)->shouldBeCalled();
        $secondNewOrderItem->setProductName('test_product_name_02')->shouldBeCalled();
        $secondNewOrderItem->setVariantName('test_variant_name_02')->shouldBeCalled();

        $orderItemQuantityModifier->modify($firstNewOrderItem, 1)->shouldBeCalled();
        $orderItemQuantityModifier->modify($secondNewOrderItem, 2)->shouldBeCalled();

        $orderModifier->addToOrder($reorder, $firstNewOrderItem)->shouldBeCalled();
        $orderModifier->addToOrder($reorder, $secondNewOrderItem)->shouldBeCalled();

        $this->createFromExistingOrder($order, $channel);
    }
}