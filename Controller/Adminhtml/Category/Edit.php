<?php

namespace Magelearn\ProductsGrid\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magelearn\ProductsGrid\Controller\Adminhtml\Category;
use Magelearn\ProductsGrid\Model\CategoryFactory;

/**
 * Class Edit
 * @package Magelearn\ProductsGrid\Controller\Adminhtml\Category
 */
class Edit extends Category
{
    /**
     * Page factory
     *
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param CategoryFactory $categoryFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CategoryFactory $categoryFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context, $registry, $categoryFactory);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|Redirect|Page
     */
    public function execute()
    {
        $category = $this->initCategory();
        if (!$category) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

            return $resultRedirect;
        }

        /**
         * Check if we have data in session
         */
        $data = $this->_session->getData('magelearn_item_category_data', true);
        if (!empty($data)) {
            $category->setData($data);
        }
            
        $this->coreRegistry->register('magelearn_item_category', $category);

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magelearn_ProductsGrid::category');
        $resultPage->getConfig()->getTitle()->set(__('Categories'));

        $title = $category->getId() ? $category->getName() : __('New Category');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
