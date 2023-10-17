<?php

namespace Magelearn\ProductsGrid\Block\Adminhtml\Category;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Magelearn\ProductsGrid\Model\Category;

/**
 * Class Edit
 * @package Magelearn\ProductsGrid\Block\Adminhtml\Category
 */
class Edit extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Edit constructor.
     *
     * @param Registry $coreRegistry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $coreRegistry,
        Context $context,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * prepare the form
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magelearn_ProductsGrid';
        $this->_controller = 'adminhtml_category';

        parent::_construct();
        
        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
    }
    
    /**
     * Retrieve text for header element depending on loaded Category
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var Tag $tag */
        $category = $this->coreRegistry->registry('magelearn_item_category');
        if ($category->getId()) {
            return __("Edit Category '%1'", $this->escapeHtml($category->getName()));
        }
        
        return __('New Category');
    }
}
