### Legacy installation (without Symfony Flex)

1. Require plugin with composer:

    ```bash
    composer require sylius/customer-reorder-plugin
    ```

2. Import configuration to `app/config/config.yml`:

    ```yaml
    imports:
        - { resource: "@SyliusCustomerReorderPlugin/Resources/config/config.yml" }
    ```

3. Import routing to `app/config/routing.yml`:

    ```yaml
    sylius_customer_reorder:
        resource: "@SyliusCustomerReorderPlugin/Resources/config/app/reorder_routing.yml"
    ```

4. Add plugin class to your `AppKernel`:

    ```php
    $bundles = [
        new \Sylius\CustomerReorderPlugin\SyliusCustomerReorderPlugin(),
    ];
    ```

5. Clear cache:

    ```bash
    bin/console cache:clear
    ```
