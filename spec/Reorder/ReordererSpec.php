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
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityChecker;
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
        ReorderEligibilityChecker $reorderEligibilityChecker
    ) {
        $this->beConstructedWith(
            $orderFactory, $entityManager, $orderProcessor, $moneyFormatter, $session, $reorderEligibilityChecker
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
        ReorderEligibilityChecker $reorderEligibilityChecker
    ) {
        $order->getTotal()->willReturn(100);
        $order->getCurrencyCode()->willReturn('USD');

        $reorder->getTotal()->willReturn(100);

        $orderFactory->createFromExistingOrder($order, $channel)->willReturn($reorder);
        $entityManager->persist($reorder)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $reorderEligibilityChecker->check($order, $reorder)->willReturn([]);

        $this->reorder($order, $channel);
    }

    function it_notifies_when_orders_totals_differ(
        OrderFactoryInterface $orderFactory,
        EntityManagerInterface $entityManager,
        ChannelInterface $channel,
        OrderInterface $order,
        OrderInterface $reorder,
        MoneyFormatterInterface $moneyFormatter,
        Session $session,
        FlashBagInterface $flashBag,
        ArrayCollection $promotions,
        ReorderEligibilityChecker $reorderEligibilityChecker
    ) {
        $order->getTotal()->willReturn(100);
        $order->getCurrencyCode()->willReturn('USD');
        $order->getPromotions()->willReturn($promotions);

        $reorder->getTotal()->willReturn(150);
        $reorder->getPromotions()->willReturn($promotions);

        $moneyFormatter->format(100, 'USD')->willReturn('$1.00');

        $reorderEligibilityChecker->check($order, $reorder)->willReturn([
            [
                'type' => 'info',
                'message' => 'sylius.reorder.previous_order_total',
                'parameters' => ['%order_total%' => '$1.00']
            ]
        ]);

        $session->getFlashBag()->willReturn($flashBag);
        $flashBag->add('info', [
            'message' => 'sylius.reorder.previous_order_total',
            'parameters' => ['%order_total%' => '$1.00']
        ])->shouldBeCalled();

        $orderFactory->createFromExistingOrder($order, $channel)->willReturn($reorder);
        $entityManager->persist($reorder)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->reorder($order, $channel);
    }

    function it_notifies_when_promotion_is_no_longer_available(
        OrderFactoryInterface $orderFactory,
        EntityManagerInterface $entityManager,
        ChannelInterface $channel,
        OrderInterface $order,
        OrderInterface $reorder,
        MoneyFormatterInterface $moneyFormatter,
        Session $session,
        FlashBagInterface $flashBag,
        PromotionInterface $firstPromotion,
        PromotionInterface $secondPromotion,
        ReorderEligibilityChecker $reorderEligibilityChecker
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

        $reorderEligibilityChecker->check($order, $reorder)->willReturn([
            [
                'type' => 'info',
                'message' => 'sylius.reorder.promotion_not_enabled',
                'parameters' => [
                    '%promotions%' => 'test_promotion_01, test_promotion02'
                ]
            ],
            [
                'type' => 'info',
                'message' => 'sylius.reorder.previous_order_total',
                'parameters' => ['%order_total%' => '$1.00']
            ]
        ]);

        $session->getFlashBag()->willReturn($flashBag);
        $flashBag->add('info',
            [
                'message' => 'sylius.reorder.promotion_not_enabled',
                'parameters' => [
                    '%promotions%' => 'test_promotion_01, test_promotion02'
                ]
            ]
        )->shouldBeCalled();
        $flashBag->add('info', [
            'message' => 'sylius.reorder.previous_order_total',
            'parameters' => [
                '%order_total%' => '$1.00'
            ]
        ])->shouldBeCalled();

        $orderFactory->createFromExistingOrder($order, $channel)->willReturn($reorder);
        $entityManager->persist($reorder)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->reorder($order, $channel);
    }
}
