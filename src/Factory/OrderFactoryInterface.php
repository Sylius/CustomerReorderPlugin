<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface OrderFactoryInterface
{
    public function createFromExistingOrder(OrderInterface $order, ChannelInterface $channel): OrderInterface;
}
