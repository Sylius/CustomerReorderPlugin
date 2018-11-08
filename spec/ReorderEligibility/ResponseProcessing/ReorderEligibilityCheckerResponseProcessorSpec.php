<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing;

use PhpSpec\ObjectBehavior;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityCheckerResponse;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\EligibilityCheckerFailureResponses;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\ReorderEligibilityCheckerResponseProcessor;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\ReorderEligibilityCheckerResponseProcessorInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;

final class ReorderEligibilityCheckerResponseProcessorSpec extends ObjectBehavior
{
    function let(Session $session): void
    {
        $this->beConstructedWith($session);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ReorderEligibilityCheckerResponseProcessor::class);
    }

    function it_implements_reorder_eligibility_checker_response_processor_interface(): void
    {
        $this->shouldImplement(ReorderEligibilityCheckerResponseProcessorInterface::class);
    }

    function it_adds_flash_bag_messages_based_on_given_array(
        ReorderEligibilityCheckerResponse $firstResponse,
        ReorderEligibilityCheckerResponse $secondResponse,
        ReorderEligibilityCheckerResponse $thirdResponse,
        Session $session,
        FlashBagInterface $flashBag
    ): void {
        $firstResponse->getMessage()->willReturn(EligibilityCheckerFailureResponses::REORDER_ITEMS_PRICES_CHANGED);
        $firstResponse->getParameters()->willReturn(['%product_names%' => 'test_product_01']);

        $secondResponse->getMessage()->willReturn(EligibilityCheckerFailureResponses::ITEMS_OUT_OF_STOCK);
        $secondResponse->getParameters()->willReturn(['%order_items%' => 'test_item_01']);

        $thirdResponse->getMessage()->willReturn(EligibilityCheckerFailureResponses::TOTAL_AMOUNT_CHANGED);
        $thirdResponse->getParameters()->willReturn(['%order_total%' => '$100.00']);

        $session->getFlashBag()->willReturn($flashBag);

        $flashBag->add('info', [
            'message' => EligibilityCheckerFailureResponses::REORDER_ITEMS_PRICES_CHANGED,
            'parameters' => [
                '%product_names%' => 'test_product_01',
            ],
        ])->shouldBeCalled();

        $flashBag->add('info', [
            'message' => EligibilityCheckerFailureResponses::ITEMS_OUT_OF_STOCK,
            'parameters' => [
                '%order_items%' => 'test_item_01',
            ],
        ])->shouldBeCalled();

        $flashBag->add('info', [
            'message' => EligibilityCheckerFailureResponses::TOTAL_AMOUNT_CHANGED,
            'parameters' => [
                '%order_total%' => '$100.00',
            ],
        ])->shouldBeCalled();

        $this->process([$firstResponse, $secondResponse, $thirdResponse]);
    }
}
