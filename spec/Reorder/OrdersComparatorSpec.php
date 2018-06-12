<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\Reorder;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\CustomerReorderPlugin\Reorder\OrdersComparator;
use Sylius\CustomerReorderPlugin\Reorder\OrdersComparatorInterface;

final class OrdersComparatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OrdersComparator::class);
    }

    function it_implements_order_to_reorder_comparator_interface()
    {
        $this->shouldImplement(OrdersComparatorInterface::class);
    }

    function it_checks_if_applied_promotions_differ(
        OrderInterface $firstOrder,
        OrderInterface $secondOrder,
        PromotionInterface $firstPromotion,
        PromotionInterface $secondPromotion
    ) {
        $firstOrder->getPromotions()->willReturn(new ArrayCollection([$firstPromotion->getWrappedObject()]));
        $secondOrder->getPromotions()->willReturn(new ArrayCollection([$secondPromotion->getWrappedObject()]));

        $this->hasAnyPromotionChanged($firstOrder, $secondOrder)->shouldReturn(true);
    }

    function it_checks_if_order_items_prices_differ(
        OrderInterface $firstOrder,
        OrderInterface $secondOrder,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem
    ) {
        $firstOrder->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject()
        ]));

        $secondOrder->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject()
        ]));

        $firstOrderItem->getVariantName()->willReturn('test_variant_name_01');
        $firstOrderItem->getUnitPrice()->willReturn(10);

        $secondOrderItem->getVariantName()->willReturn('test_variant_name_02');
        $secondOrderItem->getUnitPrice()->willReturn(10, 20);

        $this->hasAnyVariantPriceChanged($firstOrder, $secondOrder)->shouldReturn(true);
    }
}
