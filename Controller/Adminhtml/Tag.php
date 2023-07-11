<?php

namespace Magelearn\ProductsGrid\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magelearn\ProductsGrid\Model\TagFactory;

/**
 * Class Tag
 * @package Magelearn\ProductsGrid\Controller\Adminhtml
 */
abstract class Tag extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Magelearn_ProductsGrid::tag';

    /**
     * Tag Factory
     *
     * @var TagFactory
     */
    public $tagFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Tag constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param TagFactory $tagFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        TagFactory $tagFactory
    ) {
        $this->tagFactory = $tagFactory;
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     *
     * @return bool|\Magelearn\ProductsGrid\Model\Tag
     */
    protected function initTag($register = false)
    {
        $tagId = (int)$this->getRequest()->getParam('id');

        /** @var \Magelearn\ProductsGrid\Model\Tag $tag */
        $tag = $this->tagFactory->create();
        if ($tagId) {
            $tag->load($tagId);
            if (!$tag->getId()) {
                $this->messageManager->addErrorMessage(__('This tag no longer exists.'));

                return false;
            }
        }

        if ($register) {
            $this->coreRegistry->register('magelearn_item_tag', $tag);
        }

        return $tag;
    }
}
