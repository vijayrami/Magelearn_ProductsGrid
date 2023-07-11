<?php

namespace Magelearn\ProductsGrid\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magelearn\ProductsGrid\Model\CategoryFactory;

/**
 * Class Category
 * @package Magelearn\ProductsGrid\Controller\Adminhtml
 */
abstract class Category extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Magelearn_ProductsGrid::category';

    /**
     * Blog Category Factory
     *
     * @var CategoryFactory
     */
    public $categoryFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Category constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        CategoryFactory $categoryFactory
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     * @param bool $isSave
     *
     * @return bool|\Magelearn\ProductsGrid\Model\Category
     */
    public function initCategory($register = false)
    {
        $categoryId = (int)$this->getRequest()->getParam('id');

        /** @var \Magelearn\ProductsGrid\Model\Category $category */
        //Model/Category
        $category = $this->categoryFactory->create();
        if ($categoryId) {
            $category->load($categoryId);
            if (!$category->getId()) {
                $this->messageManager->addErrorMessage(__('This category no longer exists.'));
                
                return false;
            }
        }

        if ($register) {
            $this->coreRegistry->register('magelearn_item_category', $category);
        }

        return $category;
    }
}
