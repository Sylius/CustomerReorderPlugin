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

    public function check(OrderInterface $order, OrderInterface $reorder): ReorderEligibilityCheckerResponse
    {
        $response = new ReorderEligibilityCheckerResponse();

        if ($order->getTotal() === $reorder->getTotal()) {
            $response->addResults([self::class => true]);
            return $response;
        }

        /** @var string */
        $currencyCode = $order->getCurrencyCode();
        $formattedTotal = $this->moneyFormatter->format($order->getTotal(), $currencyCode);

        $response->addResults([TotalReorderAmountEligibilityChecker::class => false]);
        $response->addMessages([TotalReorderAmountEligibilityChecker::class => $formattedTotal]);

        return $response;
    }
}
