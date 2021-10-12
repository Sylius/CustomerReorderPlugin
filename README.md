> :warning: **BEWARE!**
> This repository has been deprecated and will not be maintained or evolved by the Sylius Team. You can still use it with compatible Sylius versions, but at your own risk, as no bugs will be fixed on it.

<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
</p>

<h1 align="center">Customer Reorder Plugin</h1>

<p align="center"><a href="https://sylius.com/plugins/" target="_blank"><img src="https://sylius.com/assets/badge-official-sylius-plugin.png" width="200"></a></p>

<p align="center">This plugin allows customers to reorder a previously placed order.</p>

![Screenshot showing the customer's orders page with reorder buttons](docs/screenshot.png)

## Business value

The plugin allows Customer to reorder any Order that has already been placed. Once a Reorder button is clicked, a new cart 
filled with items taken from a previously placed order is created. If for some reason Reorder can't be fulfilled completely,
the Customer is informed about every circumstance that have affected the Order (i. e. promotion being no longer available
or differences in item's prices).

Once the Reorder process is completed, the newly created Order is listed in the history just like any other Orders.

## Installation

#### Beware!

> This installation instruction assumes that you're using Symfony Flex. If you don't, take a look at the
[legacy installation instruction](docs/legacy_installation.md). However, we strongly encourage you to use
Symfony Flex, it's much quicker! :)

To install plugin, just require it with composer:

```bash
composer require sylius/customer-reorder-plugin
```

> Remember to allow community recipes with `composer config extra.symfony.allow-contrib true` or during plugin installation process

## Extension points

Customer Reorder plugin is based on two processes:

* reorder processing
* reorder eligibility checking

They are both based on Symfony's compiler passes and configured in `services.xml` file.

ReorderProcessing and EligibilityChecking are independent processes - once a Reorder
is created using Processors (services tagged as `sylius_customer_reorder_plugin.reorder_processor`), the created
entity is passed to Eligibility Checkers (services tagged as `sylius_customer_reorder_plugin.eligibility_checker`).

Hence, both processes can be extended separately by adding services that implement `ReorderEligibilityChecker`
and are tagged as `sylius_customer_reorder_plugin.eligibility_checker` or implement `ReorderProcessor` and are tagged as
`sylius_customer_reorder_plugin.reorder_processor`.

Both `Reorder` button layout and action performed on clicking it are defined in
`reorder.html.twig` template which is declared in `config.yml` file.

What's more, since Order is a Resource, major part of its configuration is placed
in `*.yml` files. Without using the plugin, Order had `Show` and `Pay` actions.
Adding `Reorder` action required extending order-related behaviours in `config.yml` file.

You can read much more about Resources here:
<http://docs.sylius.com/en/1.2/components_and_bundles/bundles/SyliusResourceBundle/index.html> 

## Security issues

If you think that you have found a security issue, please do not use the issue tracker and do not post it publicly. 
Instead, all security issues must be sent to `security@sylius.com`.
