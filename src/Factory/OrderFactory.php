<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Factory;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class OrderFactory implements OrderFactoryInterface
{
    /** @var FactoryInterface */
    private $decoratedFactory;

    /** @var OrderModifierInterface */
    private $orderModifier;

    /** @var FactoryInterface */
    private $orderItemFactory;

    /** @var OrderItemQuantityModifierInterface */
    private $orderItemQuantityModifier;

    public function __construct(
        FactoryInterface $decoratedFactory,
        FactoryInterface $orderItemFactory,
        OrderModifierInterface $orderModifier,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier
    ) {
        $this->decoratedFactory = $decoratedFactory;
        $this->orderItemFactory = $orderItemFactory;
        $this->orderModifier = $orderModifier;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
    }

    public function createNew()
    {
        $order = $this->decoratedFactory->createNew();
        assert($order instanceof OrderInterface);
        return $order;
    }

    public function createFromExistingOrder(OrderInterface $order, ChannelInterface $channel): OrderInterface
    {
        $reorder = $this->decoratedFactory->createNew();
        assert($reorder instanceof OrderInterface);

        $reorder->setChannel($channel);
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

        $this->copyOrderItemsToReorder($order, $reorder);

        return $reorder;
    }

    private function copyOrderItemsToReorder(OrderInterface $order, OrderInterface $reorder): void
    {
        $orderItems = $order->getItems();

        /** @var OrderItemInterface $orderItem */
        foreach ($orderItems as $orderItem) {
            /** @var ProductVariantInterface $productVariant */
            $productVariant = $orderItem->getVariant();
            if ($productVariant->isTracked() && !$productVariant->isInStock()) {
                continue;
            }

            /** @var OrderItemInterface $newItem */
            $newItem = $this->orderItemFactory->createNew();

            $newItem->setVariant($orderItem->getVariant());
            $newItem->setUnitPrice($orderItem->getUnitPrice());
            $newItem->setProductName($orderItem->getProductName());
            $newItem->setVariantName($orderItem->getVariantName());

            $this->orderItemQuantityModifier->modify($newItem, $orderItem->getQuantity());
            $this->orderModifier->addToOrder($reorder, $newItem);
        }
    }
}
