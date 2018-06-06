SyliusCustomerReorderPlugin allows customer to reorder previously placed order.

## Installation

Require plugin with composer:

```bash
composer require sylius/customer-reorder-plugin
```

Import configuration:

```yaml
imports:
    - { resource: "@SyliusCustomerReorderPlugin/Resources/config/config.yml" }
```

Import routing:

````yaml
sylius_admin_order_creation:
    resource: "@SyliusCustomerReorderPlugin/Resources/config/app/reorder_routing.yml"
````

Add plugin class to your `AppKernel`:

```php
$bundles = [
    new \Sylius\CustomerReorderPlugin\SyliusCustomerReorderPlugin()
];
```

Clear cache:

```bash
bin/console cache:clear
```
