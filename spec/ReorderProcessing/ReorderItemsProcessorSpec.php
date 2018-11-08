<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\ReorderProcessing;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\CustomerReorderPlugin\ReorderProcessing\ReorderItemsProcessor;
use Sylius\CustomerReorderPlugin\ReorderProcessing\ReorderProcessor;

final class ReorderItemsProcessorSpec extends ObjectBehavior
{
    function let(
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderModifierInterface $orderModifier,
        AvailabilityCheckerInterface $availabilityChecker,
        FactoryInterface $orderItemFactory
    ): void {
        $this->beConstructedWith($orderItemQuantityModifier, $orderModifier, $availabilityChecker, $orderItemFactory);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ReorderItemsProcessor::class);
    }

    function it_implements_reorder_processor_interface(): void
    {
        $this->shouldImplement(ReorderProcessor::class);
    }

    function it_copies_order_items_to_reorder(
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderModifierInterface $orderModifier,
        AvailabilityCheckerInterface $availabilityChecker,
        FactoryInterface $orderItemFactory,
        OrderInterface $order,
        OrderInterface $reorder,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        ProductVariantInterface $firstProductVariant,
        ProductVariantInterface $secondProductVariant,
        OrderItemInterface $firstNewOrderItem,
        OrderItemInterface $secondNewOrderItem
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject(),
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

        $availabilityChecker->isStockAvailable($firstProductVariant)->willReturn(true);
        $availabilityChecker->isStockAvailable($secondProductVariant)->willReturn(true);

        $availabilityChecker->isStockSufficient($firstProductVariant, 1)->willReturn(true);
        $availabilityChecker->isStockSufficient($secondProductVariant, 2)->willReturn(true);

        $orderItemFactory->createNew()->willReturn($firstNewOrderItem, $secondNewOrderItem);

        $firstProductVariant->isTracked()->willReturn(true);
        $firstProductVariant->isInStock()->willReturn(true);
        $firstProductVariant->getOnHand()->willReturn(10);
        $firstProductVariant->getOnHold()->willReturn(0);

        $secondProductVariant->isTracked()->willReturn(true);
        $secondProductVariant->isInStock()->willReturn(true);
        $secondProductVariant->getOnHand()->willReturn(10);
        $secondProductVariant->getOnHold()->willReturn(0);

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

        $this->process($order, $reorder);
    }
}
