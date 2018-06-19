@reordering
Feature: Being notified about changes in order
    In order to be aware of disabled promotions or changed order total
    As a Customer
    I want to be notified about such changes when I reorder previously placed order

    Background:
        Given the store operates on a single channel in the "United States" named "Web"
        And the store has a product "Angel T-Shirt" priced at "$39.00"
        And there are 25 units of product "Angel T-Shirt" available in the inventory
        And the store has a product "Awesome Mug" priced at "$50.00"
        And there are 25 units of product "Awesome Mug" available in the inventory
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And I am a logged in customer
        And there is a promotion "Order's Extravaganza"
        And this promotion gives "$20.00" discount to every order
        And there is a promotion "Massive Order Discount"
        And this promotion gives "$10.00" discount to every order
        And I placed an order "#00000666"
        And I bought a single "Angel T-Shirt"
        And I bought a single "Awesome Mug"
        And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States", "Arkansas"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States", "Arkansas"
        And I chose "Free" shipping method with "Cash on Delivery" payment

    @ui
    Scenario: Reordering previously placed order when one of items is out of stock
        Given the product "Angel T-Shirt" is out of stock
        When I browse my orders
        And I click reorder button next to the order "#00000666"
        Then I should be on my cart summary page
        And I should be notified that product "Angel T-Shirt" is out of stock
        And I should be notified that previous order total was "$59.00"
        And I should see exactly 2 notifications

    @ui
    Scenario: Reordering previously placed order when several items are out of stock
        Given the product "Angel T-Shirt" is out of stock
        And the product "Awesome Mug" is out of stock
        When I browse my orders
        And I click reorder button next to the order "#00000666"
        Then I should be on my cart summary page
        And I should be notified that products "Angel T-Shirt", "Awesome Mug" are out of stock
        And I should be notified that previous order total was "$59.00"
        And I should see exactly 2 notifications

    @ui
    Scenario: Reordering previously placed order when promotion is no longer available
        Given the promotion was disabled for the channel "Web"
        When I browse my orders
        And I click reorder button next to the order "#00000666"
        Then I should be notified that promotion "Massive Order Discount" is no longer enabled
        And I should be notified that previous order total was "$59.00"
        And I should see exactly 2 notifications

    @ui
    Scenario: Reordering previously placed order when items' prices has changed
        Given the product "Angel T-Shirt" changed its price to "$300.00"
        When I browse my orders
        And I click reorder button next to the order "#00000666"
        Then I should be on my cart summary page
        And I should be notified that "Angel T-Shirt" price has changed
        And I should be notified that previous order total was "$59.00"
        And I should see exactly 2 notifications
