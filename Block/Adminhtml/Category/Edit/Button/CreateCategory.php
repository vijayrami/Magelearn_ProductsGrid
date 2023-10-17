<?php

namespace Magelearn\ProductsGrid\Block\Adminhtml\Category\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Button "Create Tag" in "New Post" slide-out panel of a Item page
 * Class CreateTag
 * @package Magelearn\ProductsGrid\Block\Adminhtml\Category\Edit\Button
 */
class CreateCategory implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Create Category'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 10
        ];
    }
}
