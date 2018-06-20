<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Factory;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\CustomerReorderPlugin\ReorderProcessing\ReorderProcessor;

final class OrderFactory implements OrderFactoryInterface
{
    /** @var FactoryInterface */
    private $decoratedFactory;

    /** @var ReorderProcessor */
    private $reorderProcessor;

    public function __construct(FactoryInterface $decoratedFactory, ReorderProcessor $reorderProcessor)
    {
        $this->decoratedFactory = $decoratedFactory;
        $this->reorderProcessor = $reorderProcessor;
    }

    public function createNew()
    {
        $order = $this->decoratedFactory->createNew();
        assert($order instanceof OrderInterface);
        return $order;
    }

    public function createFromExistingOrder(OrderInterface $order, ChannelInterface $channel): OrderInterface
    {
        $reorder = $this->createNew();
        assert($reorder instanceof OrderInterface);

        $reorder->setChannel($channel);
        $this->reorderProcessor->process($order, $reorder);

        return $reorder;
    }
}
