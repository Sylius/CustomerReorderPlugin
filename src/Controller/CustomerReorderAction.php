<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Controller;

use Nette\InvalidStateException;
use Sylius\Bundle\CoreBundle\Storage\CartSessionStorage;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\CustomerReorderPlugin\Reorder\ReordererInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
    private $reorderer;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var Session */
    private $session;

    /** @var CustomerInterface */
    private $customer;

    public function __construct(
        CartSessionStorage $cartSessionStorage,
        ChannelContextInterface $channelContext,
        CartContextInterface $cartContext,
        OrderRepositoryInterface $orderRepository,
        ReordererInterface $reorderService,
        UrlGeneratorInterface $urlGenerator,
        Session $session,
        CustomerInterface $customer
    ) {
        $this->cartSessionStorage = $cartSessionStorage;
        $this->channelContext = $channelContext;
        $this->cartContext = $cartContext;
        $this->orderRepository = $orderRepository;
        $this->reorderer = $reorderService;
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
        $this->customer = $customer;
    }

    public function __invoke(Request $request): Response
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->find($request->attributes->get('id'));

        if (null === $this->customer || null === $order->getCustomer() || $order->getCustomer()->getId() !== $this->customer->getId()) {
            throw new BadRequestHttpException("The customer is not the order's owner.");
        }

        $channel = $this->channelContext->getChannel();
        assert($channel instanceof ChannelInterface);

        $reorder = null;

        try {
            $reorder = $this->reorderer->reorder($order, $channel);
        } catch (InvalidStateException $exception) {
            $this->session->getFlashBag()->add('info', $exception->getMessage());

            return new RedirectResponse($this->urlGenerator->generate('sylius_shop_account_order_index'));
        }

        assert($reorder instanceof OrderInterface);

        $this->cartSessionStorage->setForChannel($channel, $reorder);

        return new RedirectResponse($this->urlGenerator->generate('sylius_shop_cart_summary'));
    }
}
