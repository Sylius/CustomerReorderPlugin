<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

final class ReorderEligibilityCheckerResponse
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

    public function addResults(array $results): void
    {
        foreach (array_keys($results) as $result) {
            $this->result[$result] = $results[$result];
        }
    }

    public function addMessages(array $messages): void
    {
        foreach (array_keys($messages) as $message) {
            $this->messages[$message] = $messages[$message];
        }
    }
}
