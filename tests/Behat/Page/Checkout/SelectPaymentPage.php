<?php

declare(strict_types=1);

namespace Tests\Sylius\CustomerReorderPlugin\Behat\Page\Checkout;

use \Sylius\Behat\Page\Shop\Checkout\SelectPaymentPage as BaseSelectPaymentPage;

final class SelectPaymentPage extends BaseSelectPaymentPage implements SelectPaymentPageInterface
{
    public function isPaymentMethodSelected(string $paymentMethodName): bool
    {
        return null !== $this
            ->getElement('payment_method_option', ['%payment_method%' => $paymentMethodName])
            ->getAttribute('checked')
        ;
    }
}
