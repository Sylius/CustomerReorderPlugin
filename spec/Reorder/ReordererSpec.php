<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\Reorder;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\CustomerReorderPlugin\Factory\OrderFactoryInterface;
use Sylius\CustomerReorderPlugin\Reorder\Reorderer;
use Sylius\CustomerReorderPlugin\Reorder\ReordererInterface;

final class ReordererSpec extends ObjectBehavior
{
    function let(
        OrderFactoryInterface $orderFactory,
        EntityManagerInterface $entityManager,
        OrderProcessorInterface $orderProcessor
    ) {
        $this->beConstructedWith($orderFactory, $entityManager, $orderProcessor);
    }

    function it_is_initializable()
    {
        $this->shouldBeAnInstanceOf(Reorderer::class);
    }

    function it_implements()
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
        $orderFactory->createFromExistingOrder($order, $channel)->willReturn($reorder);
        $entityManager->persist($reorder)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->reorder($order, $channel);
    }
}
