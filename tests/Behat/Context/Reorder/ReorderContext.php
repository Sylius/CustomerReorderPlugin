<?php

declare(strict_types=1);

namespace Tests\Sylius\CustomerReorderPlugin\Behat\Context\Reorder;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use function GuzzleHttp\Psr7\str;
use Sylius\Component\Core\Model\AddressInterface;

final class ReorderContext implements Context
{
    /** @var Session */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @Then I should see reorder button next to the order :orderNumber
     */
    public function iShouldSeeReorderButtonNextToTheOrder(string $orderNumber): void
    {
        $orderHistory = $this->session->getPage()->findAll('css', 'td');

        $isOrderPresent = false;
        $isReorderButtonVisible = false;

        foreach ($orderHistory as $orderData) {
            if (strpos($orderData->getText(), $orderNumber) != false) {
                $isOrderPresent = true;
            }

            if (strpos($orderData->getText(), 'Reorder') != false) {
                $isReorderButtonVisible = true;
            }
        }

        if (!($isOrderPresent && $isReorderButtonVisible)) {
            throw new \Exception(sprintf(
                'There is no reorder button next to order %s', $orderNumber
            ));
        }
    }

    /**
     * @When I click reorder button next to the order :orderNumber
     */
    public function iClickReorderButtonNextToTheOrder(string $orderNumber): void
    {

    }

    /**
     * @Then I should be notified that product :product is out of stock
     */
    public function iShouldBeNotifiedThatProductIsOutOfStock(string $product): void
    {

    }

    /**
     * @Then I should be notified that total price differs from previously placed order
     */
    public function iShouldBeNotifiedThatToalPriceDiffersFromPreviouslyPlacedOrder(): void
    {

    }

    /**
     * @Then /^I should have shipping address filled with (address "[^"]+", "[^"]+", "[^"]+", "[^"]+" for "[^"]+")$/
     */
    public function iShouldHaveTheAddressSectionFilledWithAddress(AddressInterface $address): void
    {

    }

    /**
     * @Then I should not have the shipping method section copied from order :orderNumber
     */
    public function iShouldNotHaveTheShippingMethodSectionFilledWithInformationTakenFromOrder(string $orderNumber): void
    {

    }

    /**
     * @Then I should not have the payment method section copied from order :orderNumber
     */
    public function iShouldNotHaveThePaymentMethodSectionFilledWithInformationTakenFromOrder(string $orderNumber): void
    {

    }
    
    /**
     * @When I proceed to the addressing step
     */
    public function iProceedToTheAddressingStep(): void
    {
        
    }

    /**
     * @When I proceed to the shipping step
     */
    public function iProceedToTheShippingStep(): void
    {

    }

    /**
     * @When I proceed to the payment step
     */
    public function iProceedToThePaymentStep(): void
    {

    }
}
