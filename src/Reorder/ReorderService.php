<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Reorder;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\CustomerReorderPlugin\Factory\OrderFactoryInterface;

final class ReorderService implements ReorderServiceInterface
{
    /** @var OrderFactoryInterface */
    private $orderFactory;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(OrderFactoryInterface $orderFactory, EntityManagerInterface $entityManager)
    {
        $this->orderFactory = $orderFactory;
        $this->entityManager = $entityManager;
    }

    public function reorder(OrderInterface $order, ChannelInterface $channel): OrderInterface
    {
        $reorder = $this->orderFactory->createFromExistingOrder($order, $channel);
        assert($reorder instanceof OrderInterface);

        $this->entityManager->persist($reorder);
        $this->entityManager->flush();

        return $reorder;
    }
}
