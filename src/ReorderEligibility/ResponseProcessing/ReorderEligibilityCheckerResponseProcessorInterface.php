<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing;

interface ReorderEligibilityCheckerResponseProcessorInterface
{
    public function process(array $responses): void;
}
