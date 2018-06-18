<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing;

use PhpSpec\ObjectBehavior;
use Sylius\CustomerReorderPlugin\ReorderEligibility\InsufficientItemQuantityEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityCheckerResponse;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\InsufficientItemQuantityEligibilityCheckerResponseProcessorInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\ReorderEligibilityCheckerResponseProcessorInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;

final class InsufficientItemQuantityEligibilityCheckerResponseProcessorSpec extends ObjectBehavior
{
    function let(Session $session): void
    {
        $this->beConstructedWith($session);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(InsufficientItemQuantityEligibilityCheckerResponseProcessorInterface::class);
    }

    function it_implements_reorder_eligibility_checker_response_processor_interface()
    {
        $this->shouldImplement(ReorderEligibilityCheckerResponseProcessorInterface::class);
    }

    function it_does_nothing_when_eligibility_checker_response_was_positive(
        ReorderEligibilityCheckerResponse $response,
        Session $session
    ): void {
        $response->getResult()->willReturn([InsufficientItemQuantityEligibilityChecker::class => true]);
        $session->getFlashBag()->shouldNotBeCalled();

        $this->process($response);
    }

    function it_adds_new_flash_message_when_response_contains_violation_message(
        ReorderEligibilityCheckerResponse $response,
        Session $session,
        FlashBagInterface $flashBag
    ): void {
        $response->getResult()->willReturn([InsufficientItemQuantityEligibilityChecker::class => false]);
        $response->getMessages()->willReturn([
            InsufficientItemQuantityEligibilityChecker::class => 'test_variant_01, test_variant_02'
        ]);

        $session->getFlashBag()->willReturn($flashBag);

        $flashBag->add('info', [
            'message' => 'sylius.reorder.insufficient_quantity',
            'parameters' => [
                '%order_items%' => 'test_variant_01, test_variant_02'
            ]
        ])->shouldBeCalled();

        $this->process($response);
    }
}
