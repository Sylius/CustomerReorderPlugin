<?php

declare(strict_types=1);

namespace Tests\Sylius\CustomerReorderPlugin\Behat\Page\Cart;

use \Sylius\Behat\Page\Shop\Cart\SummaryPageInterface as BaseSummaryPageInterface;

interface SummaryPageInterface extends BaseSummaryPageInterface
{
    public function checkout(): void;
    public function countFlashMessages(): int;
    public function doesFlashMessageWithTextExists(string $text): bool;
}
