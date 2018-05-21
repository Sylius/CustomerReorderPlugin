<?php

namespace Sylius\CustomerReorderPlugin\Reorder;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface ReordererInterface
{
    public function reorder(OrderInterface $order, ChannelInterface $channel): OrderInterface;
}
