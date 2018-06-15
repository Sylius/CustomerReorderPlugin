<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\ReorderEligibility;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ItemsOutOfStockEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityCheckerResponse;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityConstraintMessageFormatterInterface;

final class ItemsOutOfStockEligibilityCheckerSpec extends ObjectBehavior
{
    function let(
        ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter
    ): void {
        $this->beConstructedWith($reorderEligibilityConstraintMessageFormatter);
    }

    function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ItemsOutOfStockEligibilityChecker::class);
    }

    function it_implements_reorder_eligibility_checker_interface(): void
    {
        $this->shouldImplement(ReorderEligibilityChecker::class);
    }

    function it_returns_positive_result_when_all_reorder_items_are_on_hand(
        OrderInterface $order,
        OrderInterface $reorder,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        ProductVariantInterface $firstProductVariant,
        ProductVariantInterface $secondProductVariant
    ): void {
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

        $this->check($order, $reorder)->getResult()->shouldReturn([ItemsOutOfStockEligibilityChecker::class => true]);
        $this->check($order, $reorder)->getMessages()->shouldReturn([]);
    }

    function it_returns_violation_message_when_some_reorder_items_are_out_of_stock(
        OrderInterface $order,
        OrderInterface $reorder,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        ProductVariantInterface $firstProductVariant,
        ProductVariantInterface $secondProductVariant,
        ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject()
        ]));

        $firstOrderItem->getVariant()->willReturn($firstProductVariant);
        $firstOrderItem->getVariantName()->willReturn('test_name_01');
        $secondOrderItem->getVariant()->willReturn($secondProductVariant);
        $secondOrderItem->getVariantName()->willReturn('test_name_02');

        $firstProductVariant->isInStock()->willReturn(false);
        $firstProductVariant->isTracked()->willReturn(true);

        $secondProductVariant->isInStock()->willReturn(false);
        $secondProductVariant->isTracked()->willReturn(true);

        $reorderEligibilityConstraintMessageFormatter->format([
            'test_name_01',
            'test_name_02'
        ])->willReturn('test_name_01, test_name_02');

        $this->check($order, $reorder)
            ->getResult()
            ->shouldReturn([
                ItemsOutOfStockEligibilityChecker::class => false
        ]);

        $this->check($order, $reorder)
            ->getMessages()
            ->shouldReturn([
                ItemsOutOfStockEligibilityChecker::class => 'test_name_01, test_name_02'
        ]);
    }
}
