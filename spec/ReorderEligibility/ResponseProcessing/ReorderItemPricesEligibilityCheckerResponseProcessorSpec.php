<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing;

use PhpSpec\ObjectBehavior;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityCheckerResponse;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderItemPricesEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\ReorderEligibilityCheckerResponseProcessor;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\ReorderItemPricesEligibilityCheckerResponseProcessor;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;

final class ReorderItemPricesEligibilityCheckerResponseProcessorSpec extends ObjectBehavior
{
    function let(Session $session): void
    {
        $this->beConstructedWith($session);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ReorderItemPricesEligibilityCheckerResponseProcessor::class);
    }

    function it_implements_reorder_eligibility_checker_response_processor_interface()
    {
        $this->shouldImplement(ReorderEligibilityCheckerResponseProcessor::class);
    }

    function it_does_nothing_when_eligibility_checker_response_was_positive(
        ReorderEligibilityCheckerResponse $response,
        Session $session
    ): void {
        $response->getResult()->willReturn([ReorderItemPricesEligibilityChecker::class => true]);
        $session->getFlashBag()->shouldNotBeCalled();

        $this->process($response);
    }

    function it_adds_new_flash_message_when_response_contains_violation_message(
        ReorderEligibilityCheckerResponse $response,
        Session $session,
        FlashBagInterface $flashBag
    ): void {
        $response->getResult()->willReturn([ReorderItemPricesEligibilityChecker::class => false]);
        $response->getMessages()->willReturn([
            ReorderItemPricesEligibilityChecker::class => 'test_variant_01, test_variant_02'
        ]);

        $session->getFlashBag()->willReturn($flashBag);

        $flashBag->add('info', [
            'message' => 'sylius.reorder.items_price_changed',
            'parameters' => [
                '%product_names%' => 'test_variant_01, test_variant_02'
            ]
        ])->shouldBeCalled();

        $this->process($response);
    }
}
