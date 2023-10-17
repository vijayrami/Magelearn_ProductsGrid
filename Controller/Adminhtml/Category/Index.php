<?php

namespace Magelearn\ProductsGrid\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package Magelearn\ProductsGrid\Controller\Adminhtml\Category
 */
class Index extends Action
{
    /**
     * Page result factory
     *
     * @var PageFactory
     */
    public $resultPageFactory;
    
    /**
     * Page factory
     *
     * @var \Magento\Backend\Model\View\Result\Page
     */
    public $resultPage;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * execute action
     *
     * @return Forward
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Categories'));

        return $resultPage;
    }
}
