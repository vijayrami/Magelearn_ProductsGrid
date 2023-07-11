<?php

namespace Magelearn\ProductsGrid\Block\Adminhtml\Tag;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Magelearn\ProductsGrid\Model\Tag;

/**
 * Class Edit
 * @package Magelearn\ProductsGrid\Block\Adminhtml\Tag
 */
class Edit extends Container
{
    /**
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Edit constructor.
     *
     *@param Registry $coreRegistry
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
     * Initialize Tag edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magelearn_ProductsGrid';
        $this->_controller = 'adminhtml_tag';

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
     * Retrieve text for header element depending on loaded Tag
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var Tag $tag */
        $tag = $this->coreRegistry->registry('magelearn_item_tag');
        if ($tag->getId()) {
            return __("Edit Tag '%1'", $this->escapeHtml($tag->getName()));
        }

        return __('New Tag');
    }
}
