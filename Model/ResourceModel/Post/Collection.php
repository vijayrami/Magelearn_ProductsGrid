<?php

namespace Magelearn\ProductsGrid\Model\ResourceModel\Post;

use Magento\Framework\DB\Select;
use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Magelearn\ProductsGrid\Model\Post;
use Zend_Db_Select;

/**
 * Class Collection
 * @package Magelearn\ProductsGrid\Model\ResourceModel\Post
 */
class Collection extends AbstractCollection
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'item_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'magelearn_item_post_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'post_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Post::class, \Magelearn\ProductsGrid\Model\ResourceModel\Post::class);
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);

        return $countSelect;
    }

    /**
     * @param null $valueField
     * @param string $labelField
     * @param array $additional
     *
     * @return array
     */
    protected function _toOptionArray($valueField = null, $labelField = 'name', $additional = [])
    {
        $valueField = 'item_id';

        return parent::_toOptionArray($valueField, $labelField, $additional); // TODO: Change the autogenerated stub
    }
}