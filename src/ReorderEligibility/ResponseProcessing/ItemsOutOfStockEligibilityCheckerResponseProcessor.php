<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing;

use Sylius\CustomerReorderPlugin\ReorderEligibility\ItemsOutOfStockEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityCheckerResponse;
use Symfony\Component\HttpFoundation\Session\Session;

final class ItemsOutOfStockEligibilityCheckerResponseProcessor implements ReorderEligibilityCheckerResponseProcessor
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
            'message' => 'sylius.reorder.items_out_of_stock',
            'parameters' => [
                '%order_items%' => $responseMessage
            ]
        ]);
    }

    public function getClassName(): string
    {
        return ItemsOutOfStockEligibilityChecker::class;
    }
}
