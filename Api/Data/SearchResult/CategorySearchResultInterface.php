<?php

namespace Magelearn\ProductsGrid\Api\Data\SearchResult;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface CategorySearchResultInterface
 * @package Magelearn\ProductsGrid\Api\Data\SearchResult
 */
interface CategorySearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Magelearn\ProductsGrid\Api\Data\CategoryInterface[]
     */
    public function getItems();

    /**
     * @param \Magelearn\ProductsGrid\Api\Data\CategoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
