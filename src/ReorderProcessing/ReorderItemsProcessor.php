<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderProcessing;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ReorderItemsProcessor implements ReorderProcessor
{
    /** @var OrderItemQuantityModifierInterface */
    private $orderItemQuantityModifier;

    /** @var OrderModifierInterface */
    private $orderModifier;

    /** @var AvailabilityCheckerInterface */
    private $availabilityChecker;

    /** @var FactoryInterface */
    private $orderItemFactory;

    public function __construct(
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderModifierInterface $orderModifier,
        AvailabilityCheckerInterface $availabilityChecker,
        FactoryInterface $orderItemFactory
    ) {
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->orderModifier = $orderModifier;
        $this->availabilityChecker = $availabilityChecker;
        $this->orderItemFactory = $orderItemFactory;
    }

    public function process(OrderInterface $order, OrderInterface $reorder): void
    {
        $orderItems = $order->getItems();

        /** @var OrderItemInterface $orderItem */
        foreach ($orderItems as $orderItem) {
            if (null === $orderItem->getVariant() ||
                !$this->availabilityChecker->isStockAvailable($orderItem->getVariant())
            ) {
                continue;
            }

            $reorderItemQuantity = 0;

            if (!$this->availabilityChecker->isStockSufficient($orderItem->getVariant(), $orderItem->getQuantity())) {
                $reorderItemQuantity = $orderItem->getVariant()->getOnHand() - $orderItem->getVariant()->getOnHold();
            } else {
                $reorderItemQuantity = $orderItem->getQuantity();
            }

            /** @var OrderItemInterface $newItem */
            $newItem = $this->orderItemFactory->createNew();

            $newItem->setVariant($orderItem->getVariant());
            $newItem->setUnitPrice($orderItem->getUnitPrice());
            $newItem->setProductName($orderItem->getProductName());
            $newItem->setVariantName($orderItem->getVariantName());

            $this->orderItemQuantityModifier->modify($newItem, $reorderItemQuantity);
            $this->orderModifier->addToOrder($reorder, $newItem);
        }
    }
}
