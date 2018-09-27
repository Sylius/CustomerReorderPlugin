<?php

declare(strict_types=1);

namespace Tests\Sylius\CustomerReorderPlugin\Behat\Context\Reorder\Ui;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Behat\Page\Shop\Checkout\AddressPageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityConstraintMessageFormatterInterface;
use Tests\Sylius\CustomerReorderPlugin\Behat\Page\Cart\SummaryPageInterface;
use Tests\Sylius\CustomerReorderPlugin\Behat\Page\Checkout\SelectPaymentPageInterface;
use Tests\Sylius\CustomerReorderPlugin\Behat\Page\Checkout\SelectShippingPageInterface;
use Tests\Sylius\CustomerReorderPlugin\Behat\Page\Order\IndexPageInterface;
use Webmozart\Assert\Assert;

final class ReorderContext implements Context
{
    /** @var Session */
    private $session;

    /** @var ReorderEligibilityConstraintMessageFormatterInterface */
    private $reorderEligibilityConstraintMessageFormatter;

    /** @var SelectShippingPageInterface */
    private $selectShippingPage;

    /** @var SelectPaymentPageInterface */
    private $selectPaymentPage;

    /** @var AddressPageInterface */
    private $addressPage;

    /** @var SummaryPageInterface */
    private $summaryPage;

    /** @var IndexPageInterface */
    private $orderIndexPage;

    /** @var ProductVariantResolverInterface */
    private $defaultVariantResolver;

    /** @var ObjectManager */
    private $objectManager;

    public function __construct(
        Session $session,
        ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter,
        SelectShippingPageInterface $selectShippingPage,
        SelectPaymentPageInterface $selectPaymentPage,
        AddressPageInterface $addressPage,
        SummaryPageInterface $summaryPage,
        IndexPageInterface $orderIndexPage,
        ProductVariantResolverInterface $productVariantResolver,
        ObjectManager $objectManager
    ) {
        $this->session = $session;
        $this->reorderEligibilityConstraintMessageFormatter = $reorderEligibilityConstraintMessageFormatter;
        $this->selectShippingPage = $selectShippingPage;
        $this->selectPaymentPage = $selectPaymentPage;
        $this->addressPage = $addressPage;
        $this->summaryPage = $summaryPage;
        $this->orderIndexPage = $orderIndexPage;
        $this->defaultVariantResolver = $productVariantResolver;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given :count items of product :product are on hold
     */
    public function allItemsOfProductAreOnHold(int $count, ProductInterface $product): void
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);
        $productVariant->setTracked(true);
        $productVariant->setOnHold($count);

        $this->objectManager->flush();
    }

    /**
     * @Then I should be able to reorder the order :orderNumber
     */
    public function iShouldSeeReorderButtonNextToTheOrder(string $orderNumber): void
    {
        Assert::true($this->orderIndexPage->isReorderButtonVisibleNextToTheOrder($orderNumber));
    }

    /**
     * @When I reorder the order :orderNumber
     */
    public function iClickReorderButtonNextToTheOrder(string $orderNumber): void
    {
        $this->orderIndexPage->clickReorderButtonNextToTheOrder($orderNumber);
    }

    /**
     * @Then I should be notified that product :firstProduct is out of stock
     * @Then I should be notified that products :firstProduct, :secondProduct are out of stock
     */
    public function iShouldBeNotifiedThatProductIsOutOfStock(string ...$products): void
    {
        $this->summaryPage->doesFlashMessageWithTextExists(sprintf(
            'Following items: %s are out of stock, which have affected order total.',
            $this->reorderEligibilityConstraintMessageFormatter->format($products))
        );
    }

    /**
     * @Then I should be notified that product :productName is not available in expected quantity
     * @Then I should be notified that products :firstProduct, :secondProduct are not available in expected quantity
     */
    public function iShouldBeNotifiedThatUnitsOfProductWereAddedToCartInsteadOf(
        string ...$products
    ): void {
        $this->summaryPage->doesFlashMessageWithTextExists(sprintf(
            'Following items: %s are not available in expected quantity, which have affected order total.',
            $this->reorderEligibilityConstraintMessageFormatter->format($products)
        ));
    }

    /**
     * @Then I should be notified that :orderItemName price has changed
     */
    public function iShouldBeNotifiedThatOrderItemsPriceHasChanged(string $orderItemName): void
    {
        $this->summaryPage->doesFlashMessageWithTextExists(sprintf(
            'Prices of products: %s have changed, which have affected order total.',
            $orderItemName)
        );
    }

    /**
     * @Then I should be notified that previous order total was :orderTotal
     */
    public function iShouldBeNotifiedThatTotalPriceDiffersFromPreviouslyPlacedOrder(string $orderTotal): void
    {
        $this->summaryPage->doesFlashMessageWithTextExists(sprintf('Previous order total: %s', $orderTotal));
    }

    /**
     * @Then I should be notified that promotion :promotionName is no longer enabled
     */
    public function iShouldBeNotifiedThatPromotionIsNoLongerEnabled(string $promotionName): void
    {
        $this->summaryPage->doesFlashMessageWithTextExists(sprintf(
            'Following promotions: %s are no longer enabled, which have affected order total.',
            $promotionName)
        );
    }

    /**
     * @Then I should see exactly :count notification(s)
     * @Then I should not see any notifications
     */
    public function iShouldSeeExactlyNotifications(int $count = 0): void
    {
        Assert::eq($this->summaryPage->countFlashMessages(), $count);
    }

    /**
     * @Then I should not proceed to my cart summary page
     */
    public function iShouldNotProceedToMyCartSummaryPage(): void
    {
        Assert::false($this->summaryPage->isOpen());
    }

    /**
     * @Then I should be notified that none of items from previously placed order is available
     */
    public function iShouldBeNotifiedThatNoneOfItemsFromPreviouslyPlacedOrderIsAvailable(): void
    {
        $this->summaryPage->doesFlashMessageWithTextExists('None of items from previously placed order is available. Unable to place reorder.');
    }

    /**
     * @Then :shippingMethod shipping method should not be selected
     */
    public function shippingMethodShouldNotBeSelected(string $shippingMethod): void
    {
        Assert::false($this->selectShippingPage->isShippingMethodSelected($shippingMethod));
    }

    /**
     * @Then :paymentMethod payment method should not be selected
     */
    public function paymentMethodShouldNotBeSelected(string $paymentMethod): void
    {
        Assert::false($this->selectPaymentPage->isPaymentMethodSelected($paymentMethod));
    }

    /**
     * @When I proceed to the addressing step
     */
    public function iProceedToTheAddressingStep(): void
    {
        $this->summaryPage->checkout();
    }

    /**
     * @When I proceed to the shipping step
     */
    public function iProceedToTheShippingStep(): void
    {
        $this->addressPage->nextStep();
    }

    /**
     * @When I proceed to the payment step
     */
    public function iProceedToThePaymentStep(): void
    {
        $this->selectShippingPage->nextStep();
    }
}
