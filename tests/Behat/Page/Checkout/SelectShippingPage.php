<?php

declare(strict_types=1);

namespace Tests\Sylius\CustomerReorderPlugin\Behat\Page\Checkout;

use Sylius\Behat\Page\Shop\Checkout\SelectShippingPage as BaseSelectShippingPage;

final class SelectShippingPage extends BaseSelectShippingPage implements SelectShippingPageInterface
{
    public function isShippingMethodSelected(string $shippingMethodName): bool
    {
        return null !== $this
            ->getElement('shipping_method_option', ['%shipping_method%' => $shippingMethodName])
            ->getAttribute('checked')
        ;
    }
}
