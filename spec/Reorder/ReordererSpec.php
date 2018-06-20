<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\Reorder;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\CustomerReorderPlugin\Factory\OrderFactoryInterface;
use Sylius\CustomerReorderPlugin\Reorder\Reorderer;
use Sylius\CustomerReorderPlugin\Reorder\ReordererInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityCheckerResponse;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\EligibilityCheckerFailureResponses;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\ReorderEligibilityCheckerResponseProcessorInterface;
use Symfony\Component\HttpFoundation\Session\Session;

final class ReordererSpec extends ObjectBehavior
{
    function let(
        OrderFactoryInterface $orderFactory,
        EntityManagerInterface $entityManager,
        OrderProcessorInterface $orderProcessor,
        MoneyFormatterInterface $moneyFormatter,
        Session $session,
        ReorderEligibilityChecker $reorderEligibilityChecker,
        ReorderEligibilityCheckerResponseProcessorInterface $reorderEligibilityCheckerResponseProcessor
    ): void {
        $this->beConstructedWith(
            $orderFactory,
            $entityManager,
            $orderProcessor,
            $moneyFormatter,
            $session,
            $reorderEligibilityChecker,
            $reorderEligibilityCheckerResponseProcessor
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(Reorderer::class);
    }

    function it_implements_reorderer_interface(): void
    {
        $this->shouldImplement(ReordererInterface::class);
    }

    function it_creates_and_persists_reorder_from_existing_order(
        OrderFactoryInterface $orderFactory,
        EntityManagerInterface $entityManager,
        ReorderEligibilityChecker $reorderEligibilityChecker,
        ChannelInterface $channel,
        OrderInterface $order,
        OrderInterface $reorder,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem
    ): void {
        $order->getTotal()->willReturn(100);
        $order->getCurrencyCode()->willReturn('USD');

        $reorder->getTotal()->willReturn(100);

        $orderFactory->createFromExistingOrder($order, $channel)->willReturn($reorder);
        $entityManager->persist($reorder)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $reorderEligibilityChecker->check($order, $reorder)->willReturn([]);

        $reorder->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject()
        ]));

        $this->reorder($order, $channel);
    }

    function it_checks_if_orders_totals_differ(
        OrderFactoryInterface $orderFactory,
        EntityManagerInterface $entityManager,
        ReorderEligibilityChecker $reorderEligibilityChecker,
        ChannelInterface $channel,
        OrderInterface $order,
        OrderInterface $reorder,
        MoneyFormatterInterface $moneyFormatter,
        ArrayCollection $promotions,
        ReorderEligibilityCheckerResponse $reorderEligibilityCheckerResponse,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem
    ): void {
        $order->getTotal()->willReturn(100);
        $order->getCurrencyCode()->willReturn('USD');
        $order->getPromotions()->willReturn($promotions);

        $reorder->getTotal()->willReturn(150);
        $reorder->getPromotions()->willReturn($promotions);

        $reorder->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject()
        ]));

        $moneyFormatter->format(100, 'USD')->willReturn('$1.00');

        $reorderEligibilityCheckerResponse->getMessage()->willReturn(
            EligibilityCheckerFailureResponses::TOTAL_AMOUNT_CHANGED
        );
        $reorderEligibilityCheckerResponse->getParameters()->willReturn(['%order_total%' => '$1.00']);

        $reorderEligibilityChecker->check($order, $reorder)->willReturn([$reorderEligibilityCheckerResponse]);

        $orderFactory->createFromExistingOrder($order, $channel)->willReturn($reorder);
        $entityManager->persist($reorder)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->reorder($order, $channel);
    }

    function it_checks_if_promotion_is_no_longer_available(
        OrderFactoryInterface $orderFactory,
        EntityManagerInterface $entityManager,
        ReorderEligibilityChecker $reorderEligibilityChecker,
        ChannelInterface $channel,
        OrderInterface $order,
        OrderInterface $reorder,
        MoneyFormatterInterface $moneyFormatter,
        PromotionInterface $firstPromotion,
        PromotionInterface $secondPromotion,
        ReorderEligibilityCheckerResponse $reorderEligibilityCheckerResponse,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem
    ): void {
        $order->getPromotions()->willReturn(new ArrayCollection([
            $firstPromotion->getWrappedObject(),
            $secondPromotion->getWrappedObject()
        ]));

        $firstPromotion->getName()->willReturn('test_promotion_01');
        $secondPromotion->getName()->willReturn('test_promotion_02');

        $reorder->getPromotions()->willReturn(new ArrayCollection());

        $reorder->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject()
        ]));

        $moneyFormatter->format(100, 'USD')->willReturn('$1.00');

        $reorderEligibilityCheckerResponse->getMessage()->willReturn(
            EligibilityCheckerFailureResponses::REORDER_PROMOTIONS_CHANGED
        );
        $reorderEligibilityCheckerResponse->getParameters()->willReturn([
            '%promotion_names%' => 'test_promotion_01, test_promotion_02'
        ]);

        $reorderEligibilityChecker->check($order, $reorder)->willReturn([$reorderEligibilityCheckerResponse]);

        $orderFactory->createFromExistingOrder($order, $channel)->willReturn($reorder);
        $entityManager->persist($reorder)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->reorder($order, $channel);
    }

    function it_checks_if_price_of_any_item_has_changed(
        OrderFactoryInterface $orderFactory,
        EntityManagerInterface $entityManager,
        ReorderEligibilityChecker $reorderEligibilityChecker,
        ChannelInterface $channel,
        OrderInterface $order,
        OrderInterface $reorder,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        ReorderEligibilityCheckerResponse $reorderEligibilityCheckerResponse
    ): void {
        $firstOrderItem->getUnitPrice()->willReturn(100);
        $firstOrderItem->getVariantName()->willReturn('variant_name_01');

        $secondOrderItem->getUnitPrice()->willReturn(100, 150);
        $secondOrderItem->getVariantName()->willReturn('variant_name_02');

        $order->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject()
        ]));

        $reorder->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject(),
            $secondOrderItem->getWrappedObject()
        ]));

        $reorderEligibilityCheckerResponse->getMessage()->willReturn(
            EligibilityCheckerFailureResponses::REORDER_ITEMS_PRICES_CHANGED
        );

        $reorderEligibilityCheckerResponse->getParameters()->willReturn([
            '%product_names%' => 'variant_name_02'
        ]);

        $reorderEligibilityChecker->check($order, $reorder)->willReturn([$reorderEligibilityCheckerResponse]);

        $orderFactory->createFromExistingOrder($order, $channel)->willReturn($reorder);
        $entityManager->persist($reorder)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->reorder($order, $channel);
    }

    function it_checks_if_any_item_is_out_of_stock(
        OrderFactoryInterface $orderFactory,
        EntityManagerInterface $entityManager,
        ReorderEligibilityChecker $reorderEligibilityChecker,
        ChannelInterface $channel,
        OrderInterface $order,
        OrderInterface $reorder,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        ProductVariantInterface $firstProductVariant,
        ProductVariantInterface $secondProductVariant,
        ReorderEligibilityCheckerResponse $reorderEligibilityCheckerResponse
    ): void {
        $firstProductVariant->isTracked()->willReturn(true);
        $firstProductVariant->getOnHand()->willReturn(5);
        $firstProductVariant->getName()->willReturn('product_variant_01');
        $secondProductVariant->isTracked()->willReturn(true);
        $secondProductVariant->getOnHand()->willReturn(0);
        $secondProductVariant->getName()->willReturn('product_variant_02');

        $firstOrderItem->getVariant()->willReturn($firstProductVariant);
        $secondOrderItem->getVariant()->willReturn($secondProductVariant);

        $order->getItems()->willReturn(new ArrayCollection([
            $firstProductVariant->getWrappedObject(),
            $secondOrderItem->getWrappedObject()
        ]));

        $reorder->getItems()->willReturn(new ArrayCollection([
            $firstOrderItem->getWrappedObject()
        ]));

        $reorderEligibilityCheckerResponse->getMessage()->willReturn(
            EligibilityCheckerFailureResponses::ITEMS_OUT_OF_STOCK
        );
        $reorderEligibilityCheckerResponse->getParameters()->willReturn([
            '%order_items%' => 'product_variant_02'
        ]);

        $reorderEligibilityChecker->check($order, $reorder)->willReturn([$reorderEligibilityCheckerResponse]);

        $orderFactory->createFromExistingOrder($order, $channel)->willReturn($reorder);
        $entityManager->persist($reorder)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->reorder($order, $channel);
    }
}
