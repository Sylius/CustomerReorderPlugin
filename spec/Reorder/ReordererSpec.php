<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\Reorder;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\CustomerReorderPlugin\Factory\OrderFactoryInterface;
use Sylius\CustomerReorderPlugin\Reorder\Reorderer;
use Sylius\CustomerReorderPlugin\Reorder\ReordererInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;

final class ReordererSpec extends ObjectBehavior
{
    function let(
        OrderFactoryInterface $orderFactory,
        EntityManagerInterface $entityManager,
        OrderProcessorInterface $orderProcessor,
        MoneyFormatterInterface $moneyFormatter,
        Session $session
    ) {
        $this->beConstructedWith($orderFactory, $entityManager, $orderProcessor, $moneyFormatter, $session);
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
        OrderInterface $reorder
    ) {
        $order->getTotal()->willReturn(100);
        $order->getCurrencyCode()->willReturn('USD');

        $reorder->getTotal()->willReturn(100);

        $orderFactory->createFromExistingOrder($order, $channel)->willReturn($reorder);
        $entityManager->persist($reorder)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->reorder($order, $channel);
    }

    function it_notifies_when_order_total_and_reorder_total_differ(
        OrderFactoryInterface $orderFactory,
        EntityManagerInterface $entityManager,
        ChannelInterface $channel,
        OrderInterface $order,
        OrderInterface $reorder,
        MoneyFormatterInterface $moneyFormatter,
        Session $session,
        FlashBagInterface $flashBag
    ) {
        $order->getTotal()->willReturn(100);
        $order->getCurrencyCode()->willReturn('USD');

        $reorder->getTotal()->willReturn(150);

        $moneyFormatter->format(100, 'USD')->willReturn('$1.00');
        $session->getFlashBag()->willReturn($flashBag);
        $flashBag->add('info', [
            'message' => 'sylius.reorder.items_price_changed',
            'parameters' => ['%order_total%' => '$1.00']
        ])->shouldBeCalled();

        $orderFactory->createFromExistingOrder($order, $channel)->willReturn($reorder);
        $entityManager->persist($reorder)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->reorder($order, $channel);
    }
}
