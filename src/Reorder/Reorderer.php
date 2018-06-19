<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Reorder;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\CustomerReorderPlugin\Factory\OrderFactoryInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\ReorderEligibilityCheckerResponseProcessorInterface;
use Symfony\Component\HttpFoundation\Session\Session;

final class Reorderer implements ReordererInterface
{
    /** @var OrderFactoryInterface */
    private $orderFactory;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    /** @var MoneyFormatterInterface */
    private $moneyFormatter;

    /** @var Session */
    private $session;

    /** @var ReorderEligibilityChecker */
    private $reorderEligibilityChecker;

    /** @var ReorderEligibilityCheckerResponseProcessorInterface */
    private $reorderEligibilityCheckerResponseProcessor;

    public function __construct(
        OrderFactoryInterface $orderFactory,
        EntityManagerInterface $entityManager,
        OrderProcessorInterface $orderProcessor,
        MoneyFormatterInterface $moneyFormatter,
        Session $session,
        ReorderEligibilityChecker $reorderEligibilityChecker,
        ReorderEligibilityCheckerResponseProcessorInterface $reorderEligibilityCheckerResponseProcessor
    ) {
        $this->orderFactory = $orderFactory;
        $this->entityManager = $entityManager;
        $this->orderProcessor = $orderProcessor;
        $this->moneyFormatter = $moneyFormatter;
        $this->session = $session;
        $this->reorderEligibilityChecker= $reorderEligibilityChecker;
        $this->reorderEligibilityCheckerResponseProcessor= $reorderEligibilityCheckerResponseProcessor;
    }

    public function reorder(OrderInterface $order, ChannelInterface $channel): OrderInterface
    {
        $reorder = $this->orderFactory->createFromExistingOrder($order, $channel);
        assert($reorder instanceof OrderInterface);

        $reorderEligibilityChecks = $this->reorderEligibilityChecker->check($order, $reorder);
        $this->reorderEligibilityCheckerResponseProcessor->process($reorderEligibilityChecks);

        $this->entityManager->persist($reorder);
        $this->entityManager->flush();

        return $reorder;
    }
}
