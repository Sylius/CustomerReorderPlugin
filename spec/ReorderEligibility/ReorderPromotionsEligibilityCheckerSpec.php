<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\ReorderEligibility;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityConstraintMessageFormatterInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderPromotionsEligibilityChecker;

final class ReorderPromotionsEligibilityCheckerSpec extends ObjectBehavior
{
    function let(ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter)
    {
        $this->beConstructedWith($reorderEligibilityConstraintMessageFormatter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ReorderPromotionsEligibilityChecker::class);
    }

    function it_implements_reorder_eligibility_checker_interface()
    {
        $this->shouldImplement(ReorderEligibilityChecker::class);
    }

    function it_returns_empty_array_when_there_are_no_reorder_items(OrderInterface $order, OrderInterface $reorder)
    {
        $reorder->getItems()->willReturn(new ArrayCollection());

        $this->check($order, $reorder)->shouldReturn([]);
    }

    function it_returns_empty_array_when_the_same_promotions_are_applied(
        OrderInterface $order,
        OrderInterface $reorder,
        OrderItemInterface $reorderItem,
        PromotionInterface $firstPromotion,
        PromotionInterface $secondPromotion
    ) {
        $reorder->getItems()->willReturn(new ArrayCollection([
            $reorderItem->getWrappedObject()
        ]));

        $order->getPromotions()->willReturn(new ArrayCollection([
            $firstPromotion->getWrappedObject(),
            $secondPromotion->getWrappedObject()
        ]));

        $reorder->getPromotions()->willReturn(new ArrayCollection([
            $firstPromotion->getWrappedObject(),
            $secondPromotion->getWrappedObject()
        ]));

        $this->check($order, $reorder)->shouldReturn([]);
    }

    function it_returns_violation_message_when_some_promotions_are_not_applied(
        OrderInterface $order,
        OrderInterface $reorder,
        OrderItemInterface $reorderItem,
        PromotionInterface $firstPromotion,
        PromotionInterface $secondPromotion,
        ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter
    ) {
        $reorder->getItems()->willReturn(new ArrayCollection([
            $reorderItem->getWrappedObject()
        ]));

        $order->getPromotions()->willReturn(new ArrayCollection([
            $firstPromotion->getWrappedObject(),
            $secondPromotion->getWrappedObject()
        ]));

        $reorder->getPromotions()->willReturn(new ArrayCollection());

        $firstPromotion->getName()->willReturn('test_promotion_01');
        $secondPromotion->getName()->willReturn('test_promotion_02');

        $reorderEligibilityConstraintMessageFormatter->format([
            'test_promotion_01',
            'test_promotion_02'
        ])->willReturn('test_promotion_01, test_promotion_02');

        $this->check($order, $reorder)->shouldReturn([
            'type' => 'info',
            'message' => 'sylius.reorder.promotion_not_enabled',
            'parameters' => [
                '%promotion_names%' => 'test_promotion_01, test_promotion_02'
            ]
        ]);
    }
}
