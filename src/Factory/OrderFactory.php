<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\CustomerReorderPlugin\ReorderProcessing\ReorderProcessor;

final class OrderFactory implements OrderFactoryInterface
{
    /** @var FactoryInterface */
    private $baseOrderFactory;

    /** @var ReorderProcessor */
    private $reorderProcessor;

    public function __construct(FactoryInterface $baseOrderFactory, ReorderProcessor $reorderProcessor)
    {
        $this->baseOrderFactory = $baseOrderFactory;
        $this->reorderProcessor = $reorderProcessor;
    }

    public function createFromExistingOrder(OrderInterface $order, ChannelInterface $channel): OrderInterface
    {
        $reorder = $this->baseOrderFactory->createNew();
        assert($reorder instanceof OrderInterface);

        $reorder->setChannel($channel);
        $this->reorderProcessor->process($order, $reorder);

        return $reorder;
    }
}
