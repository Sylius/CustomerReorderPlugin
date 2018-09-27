<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Controller;

use Nette\InvalidStateException;
use Sylius\Bundle\CoreBundle\Storage\CartSessionStorage;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\CustomerReorderPlugin\Reorder\ReordererInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CustomerReorderAction
{
    /** @var CartSessionStorage */
    private $cartSessionStorage;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var CartContextInterface */
    private $cartContext;

    /** @var CustomerContextInterface */
    private $customerContext;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var ReordererInterface */
    private $reorderer;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var Session */
    private $session;

    public function __construct(
        CartSessionStorage $cartSessionStorage,
        ChannelContextInterface $channelContext,
        CartContextInterface $cartContext,
        CustomerContextInterface $customerContext,
        OrderRepositoryInterface $orderRepository,
        ReordererInterface $reorderService,
        UrlGeneratorInterface $urlGenerator,
        Session $session
    ) {
        $this->cartSessionStorage = $cartSessionStorage;
        $this->channelContext = $channelContext;
        $this->cartContext = $cartContext;
        $this->customerContext = $customerContext;
        $this->orderRepository = $orderRepository;
        $this->reorderer = $reorderService;
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
    }

    public function __invoke(Request $request): Response
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->find($request->attributes->get('id'));

        $channel = $this->channelContext->getChannel();
        assert($channel instanceof ChannelInterface);

        /** @var CustomerInterface $customer */
        $customer = $this->customerContext->getCustomer();

        $reorder = null;

        try {
            $reorder = $this->reorderer->reorder($order, $channel, $customer);
        } catch (InvalidStateException $exception) {
            $this->session->getFlashBag()->add('info', $exception->getMessage());

            return new RedirectResponse($this->urlGenerator->generate('sylius_shop_account_order_index'));
        }

        assert($reorder instanceof OrderInterface);

        $this->cartSessionStorage->setForChannel($channel, $reorder);

        return new RedirectResponse($this->urlGenerator->generate('sylius_shop_cart_summary'));
    }
}
