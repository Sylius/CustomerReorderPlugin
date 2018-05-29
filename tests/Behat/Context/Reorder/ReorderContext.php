<?php

declare(strict_types=1);

namespace Tests\Sylius\CustomerReorderPlugin\Behat\Context\Reorder;

use Behat\Behat\Context\Context;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\PromotionInterface;

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
        $orderData = $this->session->getPage()->find('css', sprintf('tr:contains("%s")', $orderNumber));

        if (null === $orderData) {
            throw new \Exception(sprintf('There is no order %s on the orders list', $orderNumber));
        }

        $actionButtonsText = $orderData->find('css', 'td:last-child')->getText();

        if (!strpos($actionButtonsText, 'Reorder')) {
            throw new \Exception(sprintf('There is no reorder button next to order %s', $orderNumber));
        }
    }

    /**
     * @When I click reorder button next to the order :orderNumber
     */
    public function iClickReorderButtonNextToTheOrder(string $orderNumber): void
    {
        $orderData = $this->session->getPage()->find('css', sprintf('tr:contains("%s")', $orderNumber));

        if (null === $orderData) {
            throw new \Exception(sprintf('There is no order %s on the orders list', $orderNumber));
        }

        $reorderButton = $orderData->find('css', 'td:last-child')->find('css', 'button');

        if (null === $reorderButton || $reorderButton->getText() != 'Reorder') {
            throw new \Exception(sprintf('There is no reorder button next to order %s', $orderNumber));
        }

        $reorderButton->click();
    }

    /**
     * @Then I should be notified that product :product is out of stock
     */
    public function iShouldBeNotifiedThatProductIsOutOfStock(string $product): void
    {

    }

    /**
     * @Then I should be notified that order items price has changed
     */
    public function iShouldBeNotifiedThatOrderItemsPriceHasChanged(): void
    {
        $this->assertFlashMessageWithTextExists('Price of some order items has changed. It may have affected order total.');
    }

    /**
     * @Then I should be notified that previous order total was :orderTotal
     */
    public function iShouldBeNotifiedThatTotalPriceDiffersFromPreviouslyPlacedOrder(string $orderTotal): void
    {
        $this->assertFlashMessageWithTextExists(sprintf('Previous order total: %s', $orderTotal));
    }

    /**
     * @Then I should be notified that promotion is no longer enabled
     */
    public function iShouldBeNotifiedThatPromotionIsNoLongerEnabled(): void
    {
        $this->assertFlashMessageWithTextExists('Some promotions are no longer enabled. It may have affected order total.');
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
        $this->session->getPage()->clickLink('Checkout');
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

    private function assertFlashMessageWithTextExists(string $text) {
        $notifications = $this->session->getPage()->findAll('css', '.sylius-flash-message');

        if (null === $notifications) {
            throw new \Exception('There is no notification on current page.');
        }

        /** @var NodeElement $notification */
        foreach ($notifications as $notification) {
            $message = $notification->getText();

            if (strpos($message, $text)) {
                return;
            }
        }

        throw new \Exception(sprintf('Flash message with text %s not found', $text));
    }
}
