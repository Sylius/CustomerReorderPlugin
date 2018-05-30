<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Component\Core\Model\OrderInterface;
use Zend\Stdlib\PriorityQueue;

final class CompositeReorderEligibilityChecker implements ReorderEligibilityChecker
{
    /**
     * @var PriorityQueue|ReorderEligibilityChecker[]
     */
    private $eligibilityCheckers;

    public function __construct()
    {
        $this->eligibilityCheckers = new PriorityQueue();
    }

    public function addProcessor(ReorderEligibilityChecker $orderProcessor, int $priority = 0): void
    {
        $this->eligibilityCheckers->insert($orderProcessor, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function check(OrderInterface $order, OrderInterface $reorder)
    {
        $result = [];
        foreach ($this->eligibilityCheckers as $eligibilityChecker) {
            array_push($result, $eligibilityChecker->check($order, $reorder));
        }

        return $result;
    }
}
