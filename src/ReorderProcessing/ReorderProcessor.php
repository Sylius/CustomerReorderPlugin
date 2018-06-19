<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderProcessing;

use Sylius\Component\Core\Model\OrderInterface;

interface ReorderProcessor
{
    function process(OrderInterface $order, OrderInterface $reorder): void;
}
