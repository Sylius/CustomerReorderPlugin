<?php

declare(strict_types=1);

namespace Tests\Sylius\CustomerReorderPlugin\Behat\Context\Reorder\Application;

use Behat\Behat\Context\Context;
use Nette\InvalidStateException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\CustomerReorderPlugin\Reorder\ReordererInterface;

final class ReorderContext implements Context
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var ReordererInterface */
    private $reorderer;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        ReordererInterface $reorderer
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->reorderer = $reorderer;
    }

    /**
     * @When the customer :customerEmail tries to reorder the order :orderNumber
     */
    public function theCustomerTriesToReorderTheOrder(string $customerEmail, string $orderNumber): void
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        /** @var CustomerInterface $customer */
        $customer = $this->customerRepository->findOneBy(['email' => $customerEmail]);

        try {
            $this->reorderer->reorder($order, $order->getChannel(), $customer);
        } catch (InvalidStateException $exception) {
            return;
        }

        throw new \Exception("Reorder should fail");
    }

    /**
     * @Then the order :orderNumber should not be reordered
     */
    public function theOrderShouldNotBeReordered(string $orderNumber): void
    {
        // skipped intentionally - not relevant as the condition was checked in previous step
    }
}
