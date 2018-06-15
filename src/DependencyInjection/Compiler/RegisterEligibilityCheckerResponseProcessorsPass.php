<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\DependencyInjection\Compiler;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;

final class RegisterEligibilityCheckerResponseProcessorsPass extends PrioritizedCompositeServicePass
{
    public function __construct()
    {
        parent::__construct(
            'Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\CompositeReorderEligibilityCheckerResponseProcessor',
            'Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\CompositeReorderEligibilityCheckerResponseProcessor',
            'sylius.reorder.eligibility_checker_response_processor',
            'addProcessor'
        );
    }
}