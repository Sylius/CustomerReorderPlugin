<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing;

use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityCheckerResponse;
use Symfony\Component\HttpFoundation\Session\Session;

final class ReorderEligibilityCheckerResponseProcessor implements ReorderEligibilityCheckerResponseProcessorInterface
{
    /** @var Session */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function process(array $responses): void
    {
        /** @var ReorderEligibilityCheckerResponse $response */
        foreach ($responses as $response) {
            $this->session->getFlashBag()->add('info', [
                'message' => $response->getMessage(),
                'parameters' => $response->getParameters(),
            ]);
        }
    }
}
