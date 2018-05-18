<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
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

    public function __construct(
        FactoryInterface $decoratedFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier
    ) {
        $this->decoratedFactory = $decoratedFactory;
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
//        $reorder->setBillingAddress($order->getBillingAddress());
//        $reorder->setShippingAddress($order->getShippingAddress());
        $reorder->setCurrencyCode($order->getCurrencyCode());
        $reorder->setCheckoutState(OrderCheckoutStates::STATE_CART);
        $reorder->setPaymentState(PaymentInterface::STATE_CART);
        $reorder->setNotes($order->getNotes());
//        $reorder->setNumber($order->getNumber());
        $reorder->setLocaleCode($order->getLocaleCode());

        return $reorder;
    }
}
