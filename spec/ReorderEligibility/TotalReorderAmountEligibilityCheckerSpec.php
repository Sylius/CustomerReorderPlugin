<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\ReorderEligibility;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\TotalReorderAmountEligibilityChecker;

final class TotalReorderAmountEligibilityCheckerSpec extends ObjectBehavior
{
    function let(MoneyFormatterInterface $moneyFormatter): void
    {
        $this->beConstructedWith($moneyFormatter);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(TotalReorderAmountEligibilityChecker::class);
    }

    function it_implements_reorder_eligibility_checker_interface(): void
    {
        $this->shouldImplement(ReorderEligibilityChecker::class);
    }

    function it_returns_positive_result_when_total_amounts_are_the_same(
        OrderInterface $order,
        OrderInterface $reorder
    ): void {
        $order->getTotal()->willReturn(100);
        $reorder->getTotal()->willReturn(100);

        $this->check($order, $reorder)->getResult()->shouldReturn([TotalReorderAmountEligibilityChecker::class => true]);
        $this->check($order, $reorder)->getMessages()->shouldReturn([]);
    }

    function it_returns_violation_message_when_total_amounts_differ(
        OrderInterface $order,
        OrderInterface $reorder,
        MoneyFormatterInterface $moneyFormatter
    ): void {
        $order->getTotal()->willReturn(100);
        $order->getCurrencyCode()->willReturn('USD');
        $reorder->getTotal()->willReturn(150);

        $moneyFormatter->format(100, 'USD')->willReturn('$100.00');

        $this->check($order, $reorder)->getResult([TotalReorderAmountEligibilityChecker::class => false]);
        $this->check($order, $reorder)->getMessages([TotalReorderAmountEligibilityChecker::class => '$100.00']);
    }
}
