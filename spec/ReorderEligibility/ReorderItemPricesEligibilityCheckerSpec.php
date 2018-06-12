<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\ReorderEligibility;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityConstraintMessageFormatterInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderItemPricesEligibilityChecker;

final class ReorderItemPricesEligibilityCheckerSpec extends ObjectBehavior
{
    function let(ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter)
    {
        $this->beConstructedWith($reorderEligibilityConstraintMessageFormatter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ReorderItemPricesEligibilityChecker::class);
    }

    function it_implements_reorder_eligibility_checker_interface()
    {
        $this->shouldImplement(ReorderEligibilityChecker::class);
    }

    function it_returns_empty_array_when_prices_are_the_same(
        OrderInterface $order,
        OrderInterface $reorder,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem
    ) {
        $order->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject()
        ]));

        $reorder->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject()
        ]));

        $firstOrderItem->getVariantName()->willReturn('test_variant_name_01');
        $firstOrderItem->getUnitPrice()->willReturn(100);

        $secondOrderItem->getVariantName()->willReturn('test_variant_name_02');
        $secondOrderItem->getUnitPrice()->willReturn(100);

        $this->check($order, $reorder)->shouldReturn([]);
    }

    function it_returns_violation_message_when_some_prices_are_different(
        OrderInterface $order,
        OrderInterface $reorder,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter
    ) {
        $order->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject()
        ]));

        $reorder->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject()
        ]));

        $firstOrderItem->getVariantName()->willReturn('test_variant_name_01');
        $firstOrderItem->getUnitPrice()->willReturn(100, 150);

        $secondOrderItem->getVariantName()->willReturn('test_variant_name_02');
        $secondOrderItem->getUnitPrice()->willReturn(100, 150);

        $reorderEligibilityConstraintMessageFormatter->format([
            'test_variant_name_01',
            'test_variant_name_02'
        ])->willReturn('test_variant_name_01, test_variant_name_02');

        $this->check($order, $reorder)->shouldReturn([
            'type' => 'info',
            'message' => 'sylius.reorder.items_price_changed',
            'parameters' => [
                '%product_names%' => 'test_variant_name_01, test_variant_name_02'
            ]
        ]);
    }
}
