<?php

declare(strict_types=1);

namespace Tests\Sylius\CustomerReorderPlugin\Behat\Page\Order;

use Behat\Mink\Element\NodeElement;
use \Sylius\Behat\Page\Shop\Account\Order\IndexPage as BaseIndexPage;

final class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function clickReorderButtonNextToTheOrder(string $orderNumber): void
    {
        $orderData = $this->getOrderData($orderNumber);

        $reorderButton = $orderData->find('css', sprintf('td button:contains("%s")', 'Reorder'));

        if (null === $reorderButton) {
            throw new \Exception(sprintf('There is no reorder button next to order %s', $orderNumber));
        }

        $reorderButton->click();
    }

    public function isReorderButtonVisibleNextToTheOrder(string $orderNumber): bool
    {
        $orderData = $this->getOrderData($orderNumber);

        $reorderButton = $orderData->find('css', sprintf('td button:contains("%s")', 'Reorder'));

        return null !== $reorderButton;
    }

    private function getOrderData(string $orderNumber): NodeElement
    {
        $orderData = $this->getSession()->getPage()->find('css', sprintf('tr:contains("%s")', $orderNumber));

        if (null === $orderData) {
            throw new \Exception(sprintf('There is no order %s on the orders list', $orderNumber));
        }

        return $orderData;
    }
}
