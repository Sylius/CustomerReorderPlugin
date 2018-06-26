<?php

declare(strict_types=1);

namespace Tests\Sylius\CustomerReorderPlugin\Behat\Page;

use \Sylius\Behat\Page\Shop\Checkout\SelectShippingPage as BaseSelectShippingPage;

final class SelectShippingPage extends BaseSelectShippingPage implements SelectShippingPageInterface
{
    public function isShippingMethodSelected(string $shippingMethodName): bool
    {
        return false;
    }
}
