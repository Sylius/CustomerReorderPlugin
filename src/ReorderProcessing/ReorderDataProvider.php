<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderProcessing;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class ReorderDataProvider implements ReorderProcessor
{
    public function process(OrderInterface $order, OrderInterface $reorder): void
    {
        $reorder->setCustomer($order->getCustomer());
        $reorder->setCurrencyCode($order->getCurrencyCode());
        $reorder->setNotes($order->getNotes());
        $reorder->setLocaleCode($order->getLocaleCode());

        /** @var AddressInterface $billingAddress */
        $billingAddress = $order->getBillingAddress();

        /** @var AddressInterface $shippingAddress */
        $shippingAddress = $order->getShippingAddress();
        $reorder->setBillingAddress(clone $billingAddress);
        $reorder->setShippingAddress(clone $shippingAddress);
    }
}
