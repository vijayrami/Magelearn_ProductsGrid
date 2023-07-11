<?php

namespace Magelearn\ProductsGrid\Controller\Adminhtml\Tag;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magelearn\ProductsGrid\Controller\Adminhtml\Tag;
use Magelearn\ProductsGrid\Model\TagFactory;

/**
 * Class Edit
 * @package Magelearn\ProductsGrid\Controller\Adminhtml\Tag
 */
class Edit extends Tag
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
     * @param TagFactory $tagFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        TagFactory $tagFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context, $registry, $tagFactory);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|Redirect|Page
     */
    public function execute()
    {
        /** @var \Magelearn\ProductsGrid\Model\Tag $tag */
        $tag = $this->initTag();
        if (!$tag) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

            return $resultRedirect;
        }

        $data = $this->_session->getData('magelearn_item_tag_data', true);
        if (!empty($data)) {
            $tag->setData($data);
        }

        $this->coreRegistry->register('magelearn_item_tag', $tag);

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magelearn_ProductsGrid::tag');
        $resultPage->getConfig()->getTitle()->set(__('Tags'));

        $title = $tag->getId() ? $tag->getName() : __('New Tag');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
