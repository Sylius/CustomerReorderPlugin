<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

final class ReorderEligibilityConstraintMessageFormatter implements ReorderEligibilityConstraintMessageFormatterInterface
{
    public function format(array $messageParameters): string
    {
        $messageParameter = '';

        if (count($messageParameters) === 1) {
            $messageParameter = array_pop($messageParameters);
        }

        else {
            $lastMessageParameter = end($messageParameters);
            foreach ($messageParameters as $messageParameter) {
                $messageParameter .= $messageParameter . ($messageParameters !== $lastMessageParameter) ? ', ' : '';
            }
        }

        return $messageParameter;
    }
}
