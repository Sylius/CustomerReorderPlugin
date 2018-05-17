<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Controller;

use Sylius\Bundle\CoreBundle\Storage\CartSessionStorage;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\CustomerReorderPlugin\Reorder\ReorderServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CustomerReorderAction
{
    /** @var CartSessionStorage */
    private $cartSessionStorage;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var CartContextInterface */
    private $cartContext;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var ReorderServiceInterface */
    private $reorderService;

    public function __construct(
        CartSessionStorage $cartSessionStorage,
        ChannelContextInterface $channelContext,
        CartContextInterface $cartContext,
        OrderRepositoryInterface $orderRepository,
        ReorderServiceInterface $reorderService
    ) {
        $this->cartSessionStorage = $cartSessionStorage;
        $this->channelContext = $channelContext;
        $this->cartContext = $cartContext;
        $this->orderRepository = $orderRepository;
        $this->reorderService = $reorderService;
    }

    public function __invoke(Request $request): Response
    {
        /** @var OrderInterface */
        $order = $this->orderRepository->find($request->query->get('id'));
        assert($order instanceof OrderInterface);

        $reorder = $this->reorderService->reorder($order);
        assert($reorder instanceof OrderInterface);

        $this->cartSessionStorage->setForChannel($reorder->getChannel(), $reorder);

        return new Response();
    }
}
