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

namespace Tests\Sylius\CustomerReorderPlugin\Behat\Context\Reorder;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\AddressInterface;

final class ReorderContext implements Context
{
    /**
     * @Then I should see reorder button next to the order :orderNumber
     */
    public function iShouldSeeReorderButtonNextToTheOrder(string $orderNumber): void
    {

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
     * @Then I should have the address section filled with address "[^"]+", "[^"]+", "[^"]+" "[^"]+" in the "[^"]+"(?:|, "[^"]+")$
     */
    public function iShouldHaveTheAddressSectionFilledWithAddress(AddressInterface $address): void
    {

    }

    /**
     * @Then I should not have the shipping method section filled with information taken from order :orderNumber
     */
    public function iShouldNotHaveTheShippingMethodSectionFilledWithInformationTakenFromOrder(string $orderNumber): void
    {

    }

    /**
     * @Then I should not have the payment method section filled with information taken from order :orderNumber
     */
    public function iShouldNotHaveThePaymentMethodSectionFilledWithInformationTakenFromOrder(string $orderNumber): void
    {

    }

}