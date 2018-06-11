<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\CustomerReorderPlugin\Factory\OrderFactory;
use Sylius\CustomerReorderPlugin\Factory\OrderFactoryInterface;
use Sylius\CustomerReorderPlugin\ReorderProcessing\ReorderProcessor;

final class OrderFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $decoratedFactory, ReorderProcessor $reorderProcessor)
    {
        $this->beConstructedWith($decoratedFactory, $reorderProcessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderFactory::class);
    }

    function it_implements_order_factory_interface()
    {
        $this->shouldImplement(OrderFactoryInterface::class);
    }

    function it_creates_new_order(FactoryInterface $decoratedFactory, OrderInterface $order)
    {
        $decoratedFactory->createNew()->willReturn($order);

        $this->createNew()->shouldReturnAnInstanceOf(OrderInterface::class);
    }

    function it_creates_reorder_from_existing_order(
        FactoryInterface $decoratedFactory,
        ReorderProcessor $reorderProcessor,
        OrderInterface $order,
        OrderInterface $reorder,
        ChannelInterface $channel
    ) {
      $decoratedFactory->createNew()->willReturn($reorder);
      $reorder->setChannel($channel)->shouldBeCalled();

      $reorderProcessor->process($order, $reorder)->shouldBeCalled();

      $this->createFromExistingOrder($order, $channel);
    }
}
