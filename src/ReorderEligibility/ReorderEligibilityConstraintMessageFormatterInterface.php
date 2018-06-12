<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

interface ReorderEligibilityConstraintMessageFormatterInterface
{
    public function format(array $messageParameters): string;
}
