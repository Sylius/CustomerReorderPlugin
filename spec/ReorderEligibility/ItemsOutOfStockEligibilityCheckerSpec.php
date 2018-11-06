<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\ReorderEligibility;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ItemsOutOfStockEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityCheckerResponse;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityConstraintMessageFormatterInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\EligibilityCheckerFailureResponses;

final class ItemsOutOfStockEligibilityCheckerSpec extends ObjectBehavior
{
    function let(
        ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter,
        AvailabilityCheckerInterface $availabilityChecker
    ): void {
        $this->beConstructedWith($reorderEligibilityConstraintMessageFormatter, $availabilityChecker);
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
        ProductVariantInterface $secondProductVariant,
        AvailabilityCheckerInterface $availabilityChecker
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject(),
        ]));

        $firstOrderItem->getVariant()->willReturn($firstProductVariant);
        $secondOrderItem->getVariant()->willReturn($secondProductVariant);

        $availabilityChecker->isStockAvailable($firstProductVariant)->willReturn(true);
        $availabilityChecker->isStockAvailable($secondProductVariant)->willReturn(true);

        $response = $this->check($order, $reorder);
        $response->shouldBeEqualTo([]);
    }

    function it_returns_violation_message_when_some_reorder_items_are_out_of_stock(
        OrderInterface $order,
        OrderInterface $reorder,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        ProductVariantInterface $firstProductVariant,
        ProductVariantInterface $secondProductVariant,
        ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter,
        AvailabilityCheckerInterface $availabilityChecker
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject(),
        ]));

        $firstOrderItem->getVariant()->willReturn($firstProductVariant);
        $firstOrderItem->getProductName()->willReturn('test_name_01');
        $secondOrderItem->getVariant()->willReturn($secondProductVariant);
        $secondOrderItem->getProductName()->willReturn('test_name_02');

        $availabilityChecker->isStockAvailable($firstProductVariant)->willReturn(false);
        $availabilityChecker->isStockAvailable($secondProductVariant)->willReturn(false);

        $reorderEligibilityConstraintMessageFormatter->format([
            'test_name_01',
            'test_name_02',
        ])->willReturn('test_name_01, test_name_02');

        $response = new ReorderEligibilityCheckerResponse();
        $response->setMessage(EligibilityCheckerFailureResponses::ITEMS_OUT_OF_STOCK);
        $response->setParameters(['%order_items%' => 'test_name_01, test_name_02']);

        $this->check($order, $reorder)->shouldBeLike([$response]);
    }
}
