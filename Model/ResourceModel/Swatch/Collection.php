<?php
namespace Magelearn\ProductsGrid\Model\ResourceModel\Swatch;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magelearn\ProductsGrid\Model\Swatch;
use Magelearn\ProductsGrid\Model\ResourceModel\Swatch as SwatchResourceModel;
use Zend_Db_Select;

class Collection extends AbstractCollection
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'swatch_id';
    
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'magelearn_item_swatch_collection';
    
    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'swatch_collection';
    
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Swatch::class, SwatchResourceModel::class);
    }
}
