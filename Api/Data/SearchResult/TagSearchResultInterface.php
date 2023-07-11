<?php

namespace Magelearn\ProductsGrid\Api\Data\SearchResult;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface TagSearchResultInterface
 * @package Magelearn\ProductsGrid\Api\Data\SearchResult
 */
interface TagSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Magelearn\ProductsGrid\Api\Data\TagInterface[]
     */
    public function getItems();

    /**
     * @param \Magelearn\ProductsGrid\Api\Data\TagInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
