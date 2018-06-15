<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\Reorder;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\CustomerReorderPlugin\Factory\OrderFactoryInterface;
use Sylius\CustomerReorderPlugin\Reorder\OrdersComparatorInterface;
use Sylius\CustomerReorderPlugin\Reorder\Reorderer;
use Sylius\CustomerReorderPlugin\Reorder\ReordererInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ItemsOutOfStockEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityCheckerResponse;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderItemPricesEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderPromotionsEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\ReorderEligibilityCheckerResponseProcessor;
use Sylius\CustomerReorderPlugin\ReorderEligibility\TotalReorderAmountEligibilityChecker;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
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
        ReorderEligibilityCheckerResponseProcessor $reorderEligibilityCheckerResponseProcessor
    ) {
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

    function it_is_initializable()
    {
        $this->shouldBeAnInstanceOf(Reorderer::class);
    }

    function it_implements_reorderer_interface()
    {
        $this->shouldImplement(ReordererInterface::class);
    }

    function it_creates_and_persists_reorder_from_existing_order(
        OrderFactoryInterface $orderFactory,
        EntityManagerInterface $entityManager,
        ChannelInterface $channel,
        OrderInterface $order,
        OrderInterface $reorder,
        ReorderEligibilityChecker $reorderEligibilityChecker,
        ReorderEligibilityCheckerResponse $reorderEligibilityCheckerResponse
    ) {
        $order->getTotal()->willReturn(100);
        $order->getCurrencyCode()->willReturn('USD');

        $reorder->getTotal()->willReturn(100);

        $orderFactory->createFromExistingOrder($order, $channel)->willReturn($reorder);
        $entityManager->persist($reorder)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $reorderEligibilityCheckerResponse->getResult()->willReturn([
            ItemsOutOfStockEligibilityChecker::class => true,
            ReorderItemPricesEligibilityChecker::class => true,
            ReorderPromotionsEligibilityChecker::class => true,
            TotalReorderAmountEligibilityChecker::class => true,
        ]);
        $reorderEligibilityCheckerResponse->getMessages()->willReturn([]);

        $reorderEligibilityChecker->check($order, $reorder)->willReturn($reorderEligibilityCheckerResponse);

        $this->reorder($order, $channel);
    }

    function it_checks_if_orders_totals_differ(
        OrderFactoryInterface $orderFactory,
        EntityManagerInterface $entityManager,
        ChannelInterface $channel,
        OrderInterface $order,
        OrderInterface $reorder,
        MoneyFormatterInterface $moneyFormatter,
        ArrayCollection $promotions,
        ReorderEligibilityChecker $reorderEligibilityChecker,
        ReorderEligibilityCheckerResponse $reorderEligibilityCheckerResponse,
        ReorderEligibilityCheckerResponseProcessor $reorderEligibilityCheckerResponseProcessor
    ) {
        $order->getTotal()->willReturn(100);
        $order->getCurrencyCode()->willReturn('USD');
        $order->getPromotions()->willReturn($promotions);

        $reorder->getTotal()->willReturn(150);
        $reorder->getPromotions()->willReturn($promotions);

        $moneyFormatter->format(100, 'USD')->willReturn('$1.00');

        $reorderEligibilityCheckerResponse->getResult()->willReturn([
            ItemsOutOfStockEligibilityChecker::class => true,
            ReorderItemPricesEligibilityChecker::class => true,
            ReorderPromotionsEligibilityChecker::class => true,
            TotalReorderAmountEligibilityChecker::class => false,
        ]);
        $reorderEligibilityCheckerResponse->getMessages()->willReturn([TotalReorderAmountEligibilityChecker::class => '$1.00']);

        $reorderEligibilityChecker->check($order, $reorder)->willReturn($reorderEligibilityCheckerResponse);

        $orderFactory->createFromExistingOrder($order, $channel)->willReturn($reorder);
        $entityManager->persist($reorder)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->reorder($order, $channel);
    }

    function it_checks_if_promotion_is_no_longer_available(
        OrderFactoryInterface $orderFactory,
        EntityManagerInterface $entityManager,
        ChannelInterface $channel,
        OrderInterface $order,
        OrderInterface $reorder,
        MoneyFormatterInterface $moneyFormatter,
        PromotionInterface $firstPromotion,
        PromotionInterface $secondPromotion,
        ReorderEligibilityChecker $reorderEligibilityChecker,
        ReorderEligibilityCheckerResponse $reorderEligibilityCheckerResponse
    ) {
        $order->getTotal()->willReturn(100);
        $order->getCurrencyCode()->willReturn('USD');
        $order->getPromotions()->willReturn(new ArrayCollection([
            $firstPromotion->getWrappedObject(),
            $secondPromotion->getWrappedObject()
        ]));

        $firstPromotion->getName()->willReturn('test_promotion_01');
        $secondPromotion->getName()->willReturn('test_promotion_02');

        $reorder->getTotal()->willReturn(150);
        $reorder->getPromotions()->willReturn(new ArrayCollection());

        $moneyFormatter->format(100, 'USD')->willReturn('$1.00');

        $reorderEligibilityCheckerResponse->getResult()->willReturn([
            ItemsOutOfStockEligibilityChecker::class => true,
            ReorderItemPricesEligibilityChecker::class => true,
            ReorderPromotionsEligibilityChecker::class => false,
            TotalReorderAmountEligibilityChecker::class => true,
        ]);
        $reorderEligibilityCheckerResponse->getMessages()->willReturn([
            ReorderPromotionsEligibilityChecker::class => 'test_promotion_01, test_promotion_02'
        ]);

        $reorderEligibilityChecker->check($order, $reorder)->willReturn($reorderEligibilityCheckerResponse);

        $orderFactory->createFromExistingOrder($order, $channel)->willReturn($reorder);
        $entityManager->persist($reorder)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->reorder($order, $channel);
    }
}
