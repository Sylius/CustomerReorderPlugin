<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\EligibilityCheckerFailureResponses;

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

        /** @var string $currencyCode */
        $currencyCode = $order->getCurrencyCode();
        $formattedTotal = $this->moneyFormatter->format($order->getTotal(), $currencyCode);

        $eligibilityCheckerResponse = new ReorderEligibilityCheckerResponse();

        $eligibilityCheckerResponse->setMessage(EligibilityCheckerFailureResponses::TOTAL_AMOUNT_CHANGED);
        $eligibilityCheckerResponse->setParameters([
            '%order_total%' => $formattedTotal,
        ]);

        return [$eligibilityCheckerResponse];
    }
}
