<?php

declare(strict_types=1);

namespace Tests\Sylius\CustomerReorderPlugin\Behat\Page\Order;

use \Sylius\Behat\Page\Shop\Account\Order\IndexPageInterface as BaseIndexPageInterface;

interface IndexPageInterface extends BaseIndexPageInterface
{
    public function clickReorderButtonNextToTheOrder(string $orderNumber): void;
    public function isReorderButtonVisibleNextToTheOrder(string $orderNumber): bool;
}
