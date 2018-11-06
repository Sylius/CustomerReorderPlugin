<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\ReorderEligibility;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityCheckerResponse;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityConstraintMessageFormatterInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderPromotionsEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\EligibilityCheckerFailureResponses;

final class ReorderPromotionsEligibilityCheckerSpec extends ObjectBehavior
{
    function let(
        ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter
    ): void {
        $this->beConstructedWith($reorderEligibilityConstraintMessageFormatter);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ReorderPromotionsEligibilityChecker::class);
    }

    function it_implements_reorder_eligibility_checker_interface(): void
    {
        $this->shouldImplement(ReorderEligibilityChecker::class);
    }

    function it_returns_positive_result_when_there_are_no_reorder_items(
        OrderInterface $order,
        OrderInterface $reorder
    ): void {
        $reorder->getItems()->willReturn(new ArrayCollection());

        $response = $this->check($order, $reorder);
        $response->shouldBeEqualTo([]);
    }

    function it_returns_positive_result_when_the_same_promotions_are_applied(
        OrderInterface $order,
        OrderInterface $reorder,
        OrderItemInterface $reorderItem,
        PromotionInterface $firstPromotion,
        PromotionInterface $secondPromotion
    ): void {
        $reorder->getItems()->willReturn(new ArrayCollection([
            $reorderItem->getWrappedObject(),
        ]));

        $order->getPromotions()->willReturn(new ArrayCollection([
            $firstPromotion->getWrappedObject(),
            $secondPromotion->getWrappedObject(),
        ]));

        $reorder->getPromotions()->willReturn(new ArrayCollection([
            $firstPromotion->getWrappedObject(),
            $secondPromotion->getWrappedObject(),
        ]));

        $response = $this->check($order, $reorder);
        $response->shouldBeEqualTo([]);
    }

    function it_returns_violation_message_when_some_promotions_are_not_applied(
        OrderInterface $order,
        OrderInterface $reorder,
        OrderItemInterface $reorderItem,
        PromotionInterface $firstPromotion,
        PromotionInterface $secondPromotion,
        ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter
    ): void {
        $reorder->getItems()->willReturn(new ArrayCollection([
            $reorderItem->getWrappedObject(),
        ]));

        $order->getPromotions()->willReturn(new ArrayCollection([
            $firstPromotion->getWrappedObject(),
            $secondPromotion->getWrappedObject(),
        ]));

        $reorder->getPromotions()->willReturn(new ArrayCollection());

        $firstPromotion->getName()->willReturn('test_promotion_01');
        $secondPromotion->getName()->willReturn('test_promotion_02');

        $reorderEligibilityConstraintMessageFormatter->format([
            'test_promotion_01',
            'test_promotion_02',
        ])->willReturn('test_promotion_01, test_promotion_02');

        $response = new ReorderEligibilityCheckerResponse();
        $response->setMessage(EligibilityCheckerFailureResponses::REORDER_PROMOTIONS_CHANGED);
        $response->setParameters([
            '%promotion_names%' => 'test_promotion_01, test_promotion_02',
        ]);

        $this->check($order, $reorder)->shouldBeLike([$response]);
    }
}
