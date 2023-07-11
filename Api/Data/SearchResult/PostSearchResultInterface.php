<?php

namespace Magelearn\ProductsGrid\Api\Data\SearchResult;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface PostSearchResultInterface
 * @api
 */
interface PostSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Magelearn\ProductsGrid\Api\Data\PostInterface[]
     */
    public function getItems();

    /**
     * @param \Magelearn\ProductsGrid\Api\Data\PostInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
