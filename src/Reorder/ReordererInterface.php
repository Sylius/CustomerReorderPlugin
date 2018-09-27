<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Reorder;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface ReordererInterface
{
    public function reorder(
        OrderInterface $order,
        ChannelInterface $channel,
        CustomerInterface $customer
    ): OrderInterface;
}
