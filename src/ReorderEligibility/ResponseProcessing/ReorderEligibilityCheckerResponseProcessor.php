<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing;

use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityCheckerResponse;

interface ReorderEligibilityCheckerResponseProcessor
{
    public function process(ReorderEligibilityCheckerResponse $response): void;
    public function getClassName(): string;
}
