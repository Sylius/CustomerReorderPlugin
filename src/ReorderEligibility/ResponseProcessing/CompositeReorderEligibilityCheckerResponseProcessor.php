<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing;

use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityCheckerResponse;
use Zend\Stdlib\PriorityQueue;

final class CompositeReorderEligibilityCheckerResponseProcessor implements ReorderEligibilityCheckerResponseProcessor
{
    /**
     * @var PriorityQueue|ReorderEligibilityCheckerResponseProcessor[]
     */
    private $eligibilityCheckerResponseProcessors;

    public function __construct()
    {
        $this->eligibilityCheckerResponseProcessors = new PriorityQueue();
    }

    public function addProcessor(ReorderEligibilityCheckerResponseProcessor $responseProcessor, int $priority = 0): void
    {
        $this->eligibilityCheckerResponseProcessors->insert($responseProcessor, $priority);
    }

    public function process(ReorderEligibilityCheckerResponse $response): void
    {
        foreach ($this->eligibilityCheckerResponseProcessors as $processor) {
            $processor->process($response);
        }
    }

    public function getClassName(): string
    {
        return '';
    }
}
