<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class TotalReorderAmountEligibilityChecker implements ReorderEligibilityChecker
{
    /** @var MoneyFormatterInterface */
    private $moneyFormatter;

    public function __construct(MoneyFormatterInterface $moneyFormatter)
    {
        $this->moneyFormatter = $moneyFormatter;
    }

    public function check(OrderInterface $order, OrderInterface $reorder): array
    {
        if ($order->getTotal() === $reorder->getTotal()) {
            return [];
        }

        /** @var string */
        $currencyCode = $order->getCurrencyCode();
        $formattedTotal = $this->moneyFormatter->format($order->getTotal(), $currencyCode);

        return [
            'type' => 'info',
            'message' => 'sylius.reorder.previous_order_total',
            'parameters' => [
                '%order_total%' => $formattedTotal
            ]
        ];
    }
}
