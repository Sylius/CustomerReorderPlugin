<?php

declare(strict_types=1);

namespace Tests\Sylius\CustomerReorderPlugin\Behat\Page\Checkout;

use \Sylius\Behat\Page\Shop\Checkout\SelectPaymentPageInterface as BaseSelectPaymentPageInterface;

interface SelectPaymentPageInterface extends BaseSelectPaymentPageInterface
{
    public function isPaymentMethodSelected(string $paymentMethodName): bool;
}
