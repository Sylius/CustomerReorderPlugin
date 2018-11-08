<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\ReorderEligibility;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\InsufficientItemQuantityEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityCheckerResponse;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityConstraintMessageFormatterInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\EligibilityCheckerFailureResponses;

final class InsufficientItemQuantityEligibilityCheckerSpec extends ObjectBehavior
{
    function let(
        ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter
    ): void {
        $this->beConstructedWith($reorderEligibilityConstraintMessageFormatter);
    }

    function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(InsufficientItemQuantityEligibilityChecker::class);
    }

    function it_implements_reorder_eligibility_checker(): void
    {
        $this->shouldImplement(ReorderEligibilityChecker::class);
    }

    function it_returns_positive_result_when_prices_are_the_same(
        OrderInterface $order,
        OrderInterface $reorder,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject(),
        ]));

        $reorder->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject(),
        ]));

        $firstOrderItem->getProductName()->willReturn('test_product_name_01');
        $firstOrderItem->getQuantity()->willReturn(100);

        $secondOrderItem->getProductName()->willReturn('test_product_name_02');
        $secondOrderItem->getQuantity()->willReturn(100);

        $response = $this->check($order, $reorder);
        $response->shouldBeEqualTo([]);
    }

    function it_returns_empty_array_when_reorder_has_no_items(
        OrderInterface $order,
        OrderInterface $reorder,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject(),
        ]));

        $reorder->getItems()->willReturn(new ArrayCollection([]));

        $firstOrderItem->getProductName()->willReturn('test_product_name_01');
        $firstOrderItem->getQuantity()->willReturn(10);

        $secondOrderItem->getProductName()->willReturn('test_product_name_02');
        $secondOrderItem->getQuantity()->willReturn(10);

        $response = $this->check($order, $reorder);
        $response->shouldBeEqualTo([]);
    }

    function it_returns_flash_message_when_reorder_items_quantity_differ(
        OrderInterface $order,
        OrderInterface $reorder,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject(),
        ]));

        $reorder->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject(),
        ]));

        $firstOrderItem->getProductName()->willReturn('test_product_name_01');
        $firstOrderItem->getQuantity()->willReturn(10, 5);

        $secondOrderItem->getProductName()->willReturn('test_product_name_02');
        $secondOrderItem->getQuantity()->willReturn(10, 5);

        $reorderEligibilityConstraintMessageFormatter->format(['test_product_name_01', 'test_product_name_02'])
            ->willReturn('test_product_name_01, test_product_name_02');

        $response = new ReorderEligibilityCheckerResponse();
        $response->setMessage(EligibilityCheckerFailureResponses::INSUFFICIENT_ITEM_QUANTITY);
        $response->setParameters([
            '%order_items%' => 'test_product_name_01, test_product_name_02',
        ]);

        $this->check($order, $reorder)->shouldBeLike([$response]);
    }
}
