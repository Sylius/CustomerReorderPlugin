<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Controller;

use Sylius\Bundle\CoreBundle\Storage\CartSessionStorage;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\CustomerReorderPlugin\Reorder\ReordererInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

    /** @var ReordererInterface */
    private $reorderService;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        CartSessionStorage $cartSessionStorage,
        ChannelContextInterface $channelContext,
        CartContextInterface $cartContext,
        OrderRepositoryInterface $orderRepository,
        ReordererInterface $reorderService,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->cartSessionStorage = $cartSessionStorage;
        $this->channelContext = $channelContext;
        $this->cartContext = $cartContext;
        $this->orderRepository = $orderRepository;
        $this->reorderService = $reorderService;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request): Response
    {
        $order = $this->orderRepository->find($request->attributes->get('id'));
        assert($order instanceof OrderInterface);

        $channel = $this->channelContext->getChannel();
        assert($channel instanceof ChannelInterface);

        $reorder = $this->reorderService->reorder($order, $channel);
        assert($reorder instanceof OrderInterface);

        $this->cartSessionStorage->setForChannel($channel, $reorder);

        return new RedirectResponse($this->urlGenerator->generate('sylius_shop_cart_summary'));
    }
}
