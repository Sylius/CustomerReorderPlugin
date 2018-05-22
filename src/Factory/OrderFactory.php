<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class OrderFactory implements OrderFactoryInterface
{
    /** @var FactoryInterface */
    private $decoratedFactory;

    /** @var OrderItemQuantityModifierInterface */
    private $orderItemQuantityModifier;

    /** @var FactoryInterface */
    private $orderItemFactory;

    public function __construct(
        FactoryInterface $decoratedFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        FactoryInterface $orderItemFactory
    ) {
        $this->decoratedFactory = $decoratedFactory;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->orderItemFactory = $orderItemFactory;
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

        $this->copyOrderItemsToReorder($order, $reorder);

        return $reorder;
    }

    private function copyOrderItemsToReorder(OrderInterface $order, OrderInterface $reorder): void
    {
        $orderItems = $order->getItems();

        foreach ($orderItems as $orderItem) {
            $reorder->addItem(clone $orderItem);
        }
    }
}
