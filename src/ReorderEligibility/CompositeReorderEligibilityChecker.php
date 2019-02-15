<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Component\Core\Model\OrderInterface;
use Zend\Stdlib\PriorityQueue;

final class CompositeReorderEligibilityChecker implements ReorderEligibilityChecker
{
    /** @var PriorityQueue|ReorderEligibilityChecker[] */
    private $eligibilityCheckers;

    public function __construct()
    {
        $this->eligibilityCheckers = new PriorityQueue();
    }

    public function addChecker(ReorderEligibilityChecker $eligibilityChecker, int $priority = 0): void
    {
        $this->eligibilityCheckers->insert($eligibilityChecker, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function check(OrderInterface $order, OrderInterface $reorder): array
    {
        $eligibilityCheckersFailures = [];

        foreach ($this->eligibilityCheckers as $eligibilityChecker) {
            $eligibilityCheckersFailures = array_merge(
                $eligibilityCheckersFailures, $eligibilityChecker->check($order, $reorder)
            );
        }

        return $eligibilityCheckersFailures;
    }
}
