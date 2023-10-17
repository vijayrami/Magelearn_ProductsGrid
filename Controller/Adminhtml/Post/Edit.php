<?php

namespace Magelearn\ProductsGrid\Controller\Adminhtml\Post;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magelearn\ProductsGrid\Controller\Adminhtml\Post;
use Magelearn\ProductsGrid\Model\PostFactory;

/**
 * Class Edit
 * @package Magelearn\ProductsGrid\Controller\Adminhtml\Post
 */
class Edit extends Post
{
    /**
     * Post Factory
     *
     * @var PostFactory
     */
    public $postFactory;
    
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
     * @param PostFactory $postFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PostFactory $postFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($postFactory, $context, $coreRegistry);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|Redirect|Page
     */
    public function execute()
    {
        /** @var \Magelearn\ProductsGrid\Model\Post $post */
        // 1. Get ID and create model
        $post = $this->initPost();
        // 2. Initial checking
        if (!$post) {
            $this->messageManager->addErrorMessage(__('This item no longer exists.'));
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*');
        }

        $data = $this->_session->getData('magelearn_item_post_data', true);
        if (!empty($data)) {
            $post->setData($data);
        }

        $this->coreRegistry->register('magelearn_item_post', $post);

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magelearn_ProductsGrid::post');
        $resultPage->getConfig()->getTitle()->set(__('Posts'));

        $title = $post->getId() ? $post->getName() : __('New Post');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
