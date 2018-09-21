<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;

final class OrderCustomerRelationChecker implements OrderCustomerRelationCheckerInterface
{
    /** @var CustomerContextInterface */
    private $customerContext;

    public function __construct(CustomerContextInterface $customerContext)
    {
        $this->customerContext = $customerContext;
    }

    public function wasOrderPlacedByCurrentCustomer(OrderInterface $order): bool
    {
        $customer = $this->customerContext->getCustomer();

        return
            null !== $customer &&
            null !g== $order->getCustomer() &&
            $order->getCustomer()->getId() === $customer->getId()
        ;
    }
}
