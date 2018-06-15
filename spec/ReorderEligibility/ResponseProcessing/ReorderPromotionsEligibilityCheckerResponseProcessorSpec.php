<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing;

use PhpSpec\ObjectBehavior;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityCheckerResponse;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderPromotionsEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\ReorderEligibilityCheckerResponseProcessor;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\ReorderPromotionsEligibilityCheckerResponseProcessor;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;

final class ReorderPromotionsEligibilityCheckerResponseProcessorSpec extends ObjectBehavior
{
    function let(Session $session): void
    {
        $this->beConstructedWith($session);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ReorderPromotionsEligibilityCheckerResponseProcessor::class);
    }

    function it_implements_reorder_eligibility_checker_response_processor_interface()
    {
        $this->shouldImplement(ReorderEligibilityCheckerResponseProcessor::class);
    }

    function it_does_nothing_when_eligibility_checker_response_was_positive(
        ReorderEligibilityCheckerResponse $response,
        Session $session
    ): void {
        $response->getResult()->willReturn([ReorderPromotionsEligibilityChecker::class => true]);
        $session->getFlashBag()->shouldNotBeCalled();

        $this->process($response);
    }

    function it_adds_new_flash_message_when_response_contains_violation_message(
        ReorderEligibilityCheckerResponse $response,
        Session $session,
        FlashBagInterface $flashBag
    ): void {
        $response->getResult()->willReturn([ReorderPromotionsEligibilityChecker::class => false]);
        $response->getMessages()->willReturn([
            ReorderPromotionsEligibilityChecker::class => 'test_promotion_01, test_promotion_02'
        ]);

        $session->getFlashBag()->willReturn($flashBag);

        $flashBag->add('info', [
            'message' => 'sylius.reorder.promotion_not_enabled',
            'parameters' => [
                '%promotion_names%' => 'test_promotion_01, test_promotion_02'
            ]
        ])->shouldBeCalled();

        $this->process($response);
    }
}
