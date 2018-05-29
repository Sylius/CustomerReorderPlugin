<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\Reorder;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\CustomerReorderPlugin\Reorder\OrderToReorderComparator;
use Sylius\CustomerReorderPlugin\Reorder\OrderToReorderComparatorInterface;

final class OrderToReorderComparatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OrderToReorderComparator::class);
    }

    function it_implements_order_to_reorder_comparator_interface()
    {
        $this->shouldImplement(OrderToReorderComparatorInterface::class);
    }

    function it_checks_if_applied_promotions_differ(
        OrderInterface $order,
        OrderInterface $reorder,
        PromotionInterface $firstPromotion,
        PromotionInterface $secondPromotion
    ) {
        $order->getPromotions()->willReturn(new ArrayCollection([$firstPromotion->getWrappedObject()]));
        $reorder->getPromotions()->willReturn(new ArrayCollection([$secondPromotion->getWrappedObject()]));

        $this->havePromotionsChanged($order, $reorder)->shouldReturn(true);
    }

    function it_checks_if_order_items_prices_differ(
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
        $firstOrderItem->getUnitPrice()->willReturn(10);

        $secondOrderItem->getVariantName()->willReturn('test_variant_name_02');
        $secondOrderItem->getUnitPrice()->willReturn(10, 20);

        $this->haveItemsPricesChanged($order, $reorder)->shouldReturn(true);
    }
}
