<?php

declare(strict_types=1);

namespace Magelearn\ProductsGrid\Api\Data;

interface SwatchSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \Magelearn\ProductsGrid\Api\Data\SwatchInterface[]
     */
    public function getItems();

    /**
     * @param \Magelearn\ProductsGrid\Api\Data\SwatchInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
