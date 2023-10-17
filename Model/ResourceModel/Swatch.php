<?php
declare(strict_types=1);

namespace Magelearn\ProductsGrid\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Swatch extends AbstractDb
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magelearn_item_post_swatch', 'swatch_id');
    }
}
