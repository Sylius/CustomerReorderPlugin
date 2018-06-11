<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Reorder;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\CustomerReorderPlugin\Factory\OrderFactoryInterface;
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

    public function __construct(
        OrderFactoryInterface $orderFactory,
        EntityManagerInterface $entityManager,
        OrderProcessorInterface $orderProcessor,
        MoneyFormatterInterface $moneyFormatter,
        Session $session
    ) {
        $this->orderFactory = $orderFactory;
        $this->entityManager = $entityManager;
        $this->orderProcessor = $orderProcessor;
        $this->moneyFormatter = $moneyFormatter;
        $this->session = $session;
    }

    public function reorder(OrderInterface $order, ChannelInterface $channel): OrderInterface
    {
        $reorder = $this->orderFactory->createFromExistingOrder($order, $channel);
        assert($reorder instanceof OrderInterface);

        if ($reorder->getTotal() !== $order->getTotal()) {
            /** @var string $orderCurrencyCode */
            $orderCurrencyCode = $order->getCurrencyCode();
            $formattedTotal = $this->moneyFormatter->format($order->getTotal(), $orderCurrencyCode);

            $this->session->getFlashBag()->add('info', [
                'message' => 'sylius.reorder.items_price_changed',
                'parameters' => ['%order_total%' => $formattedTotal]
            ]);
        }

        $this->entityManager->persist($reorder);
        $this->entityManager->flush();

        return $reorder;
    }
}
