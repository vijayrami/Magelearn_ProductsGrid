<?php

namespace Magelearn\ProductsGrid\Block\Adminhtml\Post;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Magelearn\ProductsGrid\Model\Post;

/**
 * Class Edit
 * @package Magelearn\ProductsGrid\Block\Adminhtml\Post
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
     * constructor
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
     * Initialize Post edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magelearn_ProductsGrid';
        $this->_controller = 'adminhtml_post';

        parent::_construct();
        
        $post = $this->coreRegistry->registry('magelearn_item_post');
        //$this->buttonList->remove('reset');
        $this->buttonList->update('save', 'label', __('Save Post'));
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
        
        if ($post->getId() && $this->_request->getParam('id')) {
            $this->buttonList->update('delete', 'label', __('Delete Post'));
        }
        
    }

    /**
     * Retrieve text for header element depending on loaded Post
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var Post $post */
        $post = $this->coreRegistry->registry('magelearn_item_post');

        return __('New Post');
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        /** @var Post $post */
        $post = $this->coreRegistry->registry('magelearn_item_post');
        if ($post->getId()) {
            $ar = ['id' => $post->getId()];

            return $this->getUrl('*/*/save', $ar);
        }

        return parent::getFormActionUrl();
    }
}
