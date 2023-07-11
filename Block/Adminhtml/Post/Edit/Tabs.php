<?php

namespace Magelearn\ProductsGrid\Block\Adminhtml\Post\Edit;

/**
 * Class Tabs
 * @package Magelearn\ProductsGrid\Block\Adminhtml\Post\Edit
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

        $this->setId('post_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Post Information'));
    }
}
