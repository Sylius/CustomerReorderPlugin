<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\DependencyInjection\Compiler;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;

final class RegisterReorderProcessorsPass extends PrioritizedCompositeServicePass
{
    public function __construct()
    {
        parent::__construct(
            'Sylius\CustomerReorderPlugin\ReorderProcessing\CompositeReorderProcessor',
            'Sylius\CustomerReorderPlugin\ReorderProcessing\CompositeReorderProcessor',
            'sylius_customer_reorder_plugin.reorder_processor',
            'addProcessor'
        );
    }
}
