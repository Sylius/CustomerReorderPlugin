Feature: Reordering previously placed order
    In order to reorder the same order as I placed before
    As a Customer
    I want to have items' quantity and prices recalculated during reordering process

    Background:
      Given the store operates on a single channel in the "United States" named "Web"
      And the store has a product "Angel T-Shirt" priced at "$39.00"
      And the store ships everywhere for free
      And the store allows paying with "Cash on Delivery"
      And I am a logged in customer
      And there is a promotion "Order's Extravaganza"
      And this promotion gives "$20.00" discount to every order
      And I placed an order "#00000666"
      And I bought a single "Angel T-Shirt"
      And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States"
      And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
      And I chose "Free" shipping method with "Cash on Delivery" payment

    Scenario: Reordering previously placed order
      Given I browse my orders
      When I click reorder button next to the order "#00000666"
#          Then I should be on the checkout summary step

    Scenario: Reordering previously placed order when one of items is out of stock
      Given the product "Angel T-Shirt" is out of stock
      Given I browse my orders
      When I click reorder button next to the order "#00000666"
#          Then I should be on the checkout summary step
      And I should be notified that product "Angel T-Shirt" is out of stock

    Scenario: Reordering previously placed order when promotion is no longer available
        Given the promotion was disabled for the channel "Web"
        When I browse my orders
        When I click reorder button next to the order "#00000666"
        Then I should be notified that total price differs from previously placed order

    Scenario: Having address section filled with address information taken from previously placed order
        Given I browse my orders
        When I click reorder button next to the order "#00000666"
#          Then I should be on the checkout summary step
        And I should have the address section filled with address "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States"

    Scenario: Having shipping method not filled with shipping information taken from previously placed order
        Given I browse my orders
        When I click reorder button next to the order "#00000666"
#          Then I should be on the checkout summary step
        And I should not have the shipping method section filled with information taken from order "#00000666"

    Scenario: Having payment method not filled with payment information taken from previously placed order
        Given I browse my orders
        When I click reorder button next to the order "#00000666"
#            Then I should be on the checkout summary step
        And I should not have the payment method section filled with information taken from order "#00000666"
