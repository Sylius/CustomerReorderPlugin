<?php

namespace Sylius\CustomerReorderPlugin\Reorder;

use Sylius\Component\Core\Model\OrderInterface;

interface ReorderServiceInterface
{
    public function reorder(OrderInterface $order): OrderInterface;
}
