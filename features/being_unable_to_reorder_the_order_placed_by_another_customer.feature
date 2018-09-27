@reordering
Feature: Being unable to reorder the order placed by another customer
    In order to maintain shop security
    As a Store Owner
    I want Customer to be the only person allowed to reorder their previously placed order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a customer "Rick Sanchez" identified by an email "rick.sanchez@wubba-lubba-dub-dub.com" and a password "Morty"
        And there is a customer "Morty Smith" identified by an email "morty.smith@wubba-lubba-dub-dub.com" and a password "Rick"
        And a customer "Morty Smith" placed an order "#00000666"
        And the customer bought a single "Angel T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment

    @application
    Scenario: Being unable to reorder the order placed by another customer
        When the customer "rick.sanchez@wubba-lubba-dub-dub.com" tries to reorder the order "#00000666"
        Then the order "#00000666" should not be reordered
