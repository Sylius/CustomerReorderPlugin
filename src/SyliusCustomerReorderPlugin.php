<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SyliusCustomerReorderPlugin extends Bundle
{
    use SyliusPluginTrait;
}
