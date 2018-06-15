<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

class ReorderEligibilityCheckerResponse
{
    /** @var array */
    private $result = [];

    /** @var array */
    private $messages = [];

    public function getResult(): array
    {
        return $this->result;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function addResults(array $eligibilityCheckerResults): void
    {
        foreach (array_keys($eligibilityCheckerResults) as $resultKey) {
            $this->result[$resultKey] = $eligibilityCheckerResults[$resultKey];
        }
    }

    public function addMessages(array $eligibilityCheckerMessages): void
    {
        foreach (array_keys($eligibilityCheckerMessages) as $messageKey) {
            $this->messages[$messageKey] = $eligibilityCheckerMessages[$messageKey];
        }
    }
}
