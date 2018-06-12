@reordering
Feature: Reordering previously placed order
    In order to reorder the same order as I placed before
    As a Customer
    I want to have items' quantity and prices recalculated during reordering process

    Background:
        Given the store operates on a single channel in the "United States" named "Web"
        And the store has a product "Angel T-Shirt" priced at "$39.00"
        And there are 25 units of product "Angel T-Shirt" available in the inventory
        And this product is tracked by the inventory
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And I am a logged in customer
        And there is a promotion "Order's Extravaganza"
        And this promotion gives "$20.00" discount to every order
        And I placed an order "#00000666"
        And I bought a single "Angel T-Shirt"
        And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States", "Arkansas"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States", "Arkansas"
        And I chose "Free" shipping method with "Cash on Delivery" payment

    @ui
    Scenario: Reordering previously placed order
        When I browse my orders
        And I click reorder button next to the order "#00000666"
        Then I should be on my cart summary page
        And I should see "Angel T-Shirt" with quantity 1 in my cart
        And my cart total should be "$19.00"
        And I should see exactly 0 notifications

    @ui
    Scenario: Having order's promotion applied when it's still enabled
        When I browse my orders
        And I click reorder button next to the order "#00000666"
        Then I should be on my cart summary page
        And my discount should be "-$20.00"
        And I should see exactly 0 notifications

    @ui
    Scenario: Having billing address section filled with address information taken from previously placed order
        When I browse my orders
        And I click reorder button next to the order "#00000666"
        And I proceed to the addressing step
        Then address "Mazikeen Lilim", "Pacific Coast Hwy", "90806", "Los Angeles", "United States", "Arkansas" should be filled as billing address

    @ui
    Scenario: Having shipping address section filled with address information taken from previously placed order
        When I browse my orders
        And I click reorder button next to the order "#00000666"
        And I proceed to the addressing step
        Then address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas" should be filled as shipping address

    @todo
    Scenario: Having shipping method not filled with shipping information taken from previously placed order
        Given I browse my orders
        When I click reorder button next to the order "#00000666"
        And I proceed to the shipping step
        Then I should not have the shipping method section copied from order "#00000666"

    @todo
    Scenario: Having payment method not filled with payment information taken from previously placed order
        Given I browse my orders
        When I click reorder button next to the order "#00000666"
        And I proceed to the payment step
        Then I should not have the payment method section copied from order "#00000666"
