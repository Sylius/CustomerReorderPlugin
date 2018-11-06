<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Reorder;

use Doctrine\ORM\EntityManagerInterface;
use Nette\InvalidStateException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\CustomerReorderPlugin\Checker\OrderCustomerRelationCheckerInterface;
use Sylius\CustomerReorderPlugin\Factory\OrderFactoryInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\ReorderEligibilityCheckerResponseProcessorInterface;

final class Reorderer implements ReordererInterface
{
    /** @var OrderFactoryInterface */
    private $orderFactory;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    /** @var ReorderEligibilityChecker */
    private $reorderEligibilityChecker;

    /** @var ReorderEligibilityCheckerResponseProcessorInterface */
    private $reorderEligibilityCheckerResponseProcessor;

    /** @var OrderCustomerRelationCheckerInterface */
    private $orderCustomerRelationCheckerInterface;

    public function __construct(
        OrderFactoryInterface $orderFactory,
        EntityManagerInterface $entityManager,
        OrderProcessorInterface $orderProcessor,
        ReorderEligibilityChecker $reorderEligibilityChecker,
        ReorderEligibilityCheckerResponseProcessorInterface $reorderEligibilityCheckerResponseProcessor,
        OrderCustomerRelationCheckerInterface $orderCustomerRelationChecker
    ) {
        $this->orderFactory = $orderFactory;
        $this->entityManager = $entityManager;
        $this->orderProcessor = $orderProcessor;
        $this->reorderEligibilityChecker = $reorderEligibilityChecker;
        $this->reorderEligibilityCheckerResponseProcessor = $reorderEligibilityCheckerResponseProcessor;
        $this->orderCustomerRelationCheckerInterface = $orderCustomerRelationChecker;
    }

    public function reorder(
        OrderInterface $order,
        ChannelInterface $channel,
        CustomerInterface $customer
    ): OrderInterface {
        if (!$this->orderCustomerRelationCheckerInterface->wasOrderPlacedByCustomer($order, $customer)) {
            throw new InvalidStateException("The customer is not the order's owner.");
        }

        $reorder = $this->orderFactory->createFromExistingOrder($order, $channel);
        assert($reorder instanceof OrderInterface);

        if (empty($reorder->getItems()->getValues())) {
            throw new InvalidStateException('sylius.reorder.none_of_items_is_available');
        }

        $reorderEligibilityChecks = $this->reorderEligibilityChecker->check($order, $reorder);
        $this->reorderEligibilityCheckerResponseProcessor->process($reorderEligibilityChecks);

        $this->entityManager->persist($reorder);
        $this->entityManager->flush();

        return $reorder;
    }
}
