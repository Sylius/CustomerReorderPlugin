<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\ReorderEligibility;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ItemsOutOfStockEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityConstraintMessageFormatterInterface;

final class ItemsOutOfStockEligibilityCheckerSpec extends ObjectBehavior
{
    function let(ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter)
    {
        $this->beConstructedWith($reorderEligibilityConstraintMessageFormatter);
    }

    function it_is_initializable()
    {
        $this->shouldBeAnInstanceOf(ItemsOutOfStockEligibilityChecker::class);
    }

    function it_implements_reorder_eligibility_checker_interface()
    {
        $this->shouldImplement(ReorderEligibilityChecker::class);
    }

    function it_returns_empty_array_when_all_reorder_items_are_on_hand(
        OrderInterface $order,
        OrderInterface $reorder,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        ProductVariantInterface $firstProductVariant,
        ProductVariantInterface $secondProductVariant
    ) {
        $order->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject()
        ]));

        $firstOrderItem->getVariant()->willReturn($firstProductVariant);
        $secondOrderItem->getVariant()->willReturn($secondProductVariant);

        $firstProductVariant->isInStock()->willReturn(true);
        $firstProductVariant->isTracked()->willReturn(true);

        $secondProductVariant->isInStock()->willReturn(true);
        $secondProductVariant->isTracked()->willReturn(true);

        $this->check($order, $reorder)->shouldReturn([]);
    }

    function it_returns_violation_message_when_some_reorder_items_are_out_of_stock(
        OrderInterface $order,
        OrderInterface $reorder,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        ProductVariantInterface $firstProductVariant,
        ProductVariantInterface $secondProductVariant,
        ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter
    ) {
        $order->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject()
        ]));

        $firstOrderItem->getVariant()->willReturn($firstProductVariant);
        $firstOrderItem->getVariantName()->willReturn('test_name_01');
        $secondOrderItem->getVariant()->willReturn($secondProductVariant);
        $secondOrderItem->getVariantName()->willReturn('test_name_02');

        $firstProductVariant->isInStock()->willReturn(false);
        $firstProductVariant->isTracked()->willReturn(false);

        $secondProductVariant->isInStock()->willReturn(false);
        $secondProductVariant->isTracked()->willReturn(false);

        $reorderEligibilityConstraintMessageFormatter->format([
            'test_name_01',
            'test_name_02'
        ])->willReturn('test_name_01, test_name_02');

        $this->check($order, $reorder)->shouldReturn([
            'type' => 'info',
            'message' => 'sylius.reorder.items_out_of_stock',
            'parameters' => [
                '%order_items%' => 'test_name_01, test_name_02'
            ]
        ]);
    }
}
