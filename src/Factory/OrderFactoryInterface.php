<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface OrderFactoryInterface extends FactoryInterface
{
    public function createFromExistingOrder(OrderInterface $order, ChannelInterface $channel): OrderInterface;
}
