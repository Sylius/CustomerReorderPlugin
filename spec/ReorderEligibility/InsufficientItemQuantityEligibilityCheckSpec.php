<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\ReorderEligibility;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\InsufficientItemQuantityEligibilityCheck;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityConstraintMessageFormatterInterface;

final class InsufficientItemQuantityEligibilityCheckSpec extends ObjectBehavior
{
    function let(ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter)
    {
        $this->beConstructedWith($reorderEligibilityConstraintMessageFormatter);
    }

    function it_is_initializable()
    {
        $this->shouldBeAnInstanceOf(InsufficientItemQuantityEligibilityCheck::class);
    }

    function it_implements_reorder_eligibility_checker()
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
        $firstOrderItem->getQuantity()->willReturn(100);

        $secondOrderItem->getVariantName()->willReturn('test_variant_name_02');
        $secondOrderItem->getQuantity()->willReturn(100);

        $this->check($order, $reorder)->shouldReturn([]);
    }
}
