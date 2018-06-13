<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing;

use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityCheckerResponse;
use Sylius\CustomerReorderPlugin\ReorderEligibility\TotalReorderAmountEligibilityChecker;
use Symfony\Component\HttpFoundation\Session\Session;

final class TotalReorderAmountEligibilityCheckerResponseProcessor implements ReorderEligibilityCheckerResponseProcessor
{
    /** @var Session */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function process(ReorderEligibilityCheckerResponse $response): void
    {
        if ($response->getResult()[$this->getClassName()]) {
            return;
        }

        $responseMessage = $response->getMessages()[$this->getClassName()];

        $this->session->getFlashBag()->add('info', [
            'message' => 'sylius.reorder.previous_order_total',
            'parameters' => [
                '%order_total%' => $responseMessage
            ]
        ]);
    }

    public function getClassName(): string
    {
        return TotalReorderAmountEligibilityChecker::class;
    }
}
