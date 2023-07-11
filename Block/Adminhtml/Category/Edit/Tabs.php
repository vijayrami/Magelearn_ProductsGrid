<?php

namespace Magelearn\ProductsGrid\Block\Adminhtml\Category\Edit;

/**
 * Class Tabs
 * @package Magelearn\ProductsGrid\Block\Adminhtml\Category\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('category_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Category Information'));
    }
}
