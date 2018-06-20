<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\ReorderProcessing;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\CustomerReorderPlugin\ReorderProcessing\ReorderDataProvider;
use Sylius\CustomerReorderPlugin\ReorderProcessing\ReorderProcessor;

final class ReorderDataProviderSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ReorderDataProvider::class);
    }

    function it_implements_reorder_processor_interface(): void
    {
        $this->shouldImplement(ReorderProcessor::class);
    }

    function it_copies_basic_order_data_to_reorder_instance(
        OrderInterface $order,
        OrderInterface $reorder,
        CustomerInterface $customer,
        AddressInterface $shippingAddress,
        AddressInterface $billingAddress
    ): void {
        $order->getCustomer()->willReturn($customer);
        $order->getCurrencyCode()->willReturn('USD');
        $order->getLocaleCode()->willReturn('en_US');
        $order->getNotes()->willReturn('test_notes');
        $order->getShippingAddress()->willReturn($shippingAddress);
        $order->getBillingAddress()->willReturn($billingAddress);

        $reorder->setCustomer($customer)->shouldBeCalled();
        $reorder->setCurrencyCode('USD')->shouldBeCalled();
        $reorder->setLocaleCode('en_US')->shouldBeCalled();
        $reorder->setNotes('test_notes')->shouldBeCalled();
        $reorder->setBillingAddress($billingAddress)->shouldBeCalled();
        $reorder->setShippingAddress($shippingAddress)->shouldBeCalled();

        $this->process($order, $reorder);
    }
}
