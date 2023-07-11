<?php

namespace Magelearn\ProductsGrid\Controller\Adminhtml\Category;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Messages;
use Magento\Framework\View\LayoutFactory;
use Magelearn\ProductsGrid\Controller\Adminhtml\Category;
use Magelearn\ProductsGrid\Model\CategoryFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Save
 * @package Magelearn\ProductsGrid\Controller\Adminhtml\Category
 */
class Save extends Category
{
    /**
     * Result Raw Factory
     *
     * @var RawFactory
     */
    public $resultRawFactory;

    /**
     * Result Json Factory
     *
     * @var JsonFactory
     */
    public $resultJsonFactory;

    /**
     * Layout Factory
     *
     * @var LayoutFactory
     */
    public $layoutFactory;

    /**
     * JS helper
     *
     * @var Js
     */
    public $jsHelper;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param CategoryFactory $categoryFactory
     * @param RawFactory $resultRawFactory
     * @param JsonFactory $resultJsonFactory
     * @param LayoutFactory $layoutFactory
     * @param Js $jsHelper
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        CategoryFactory $categoryFactory,
        RawFactory $resultRawFactory,
        JsonFactory $resultJsonFactory,
        LayoutFactory $layoutFactory,
        Js $jsHelper
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
        $this->jsHelper = $jsHelper;

        parent::__construct($context, $coreRegistry, $categoryFactory);
    }

    /**
     * @return Json|Redirect
     */
    public function execute()
    {
        if ($this->getRequest()->getPost('return_session_messages_only')) {
            $category = $this->initCategory();
            $categoryPostData = $this->getRequest()->getPostValue();
            $categoryPostData['enabled'] = 1;

            $category->addData($categoryPostData);

            try {
                $category->save();
                $this->messageManager->addSuccessMessage(__('You saved the category.'));
            } catch (AlreadyExistsException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_objectManager->get(LoggerInterface::class)->critical($e);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_objectManager->get(LoggerInterface::class)->critical($e);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the category.'));
                $this->_objectManager->get(LoggerInterface::class)->critical($e);
            }

            $hasError = (bool)$this->messageManager->getMessages()->getCountByType(
                MessageInterface::TYPE_ERROR
            );
            
            $category->load($category->getId());
            $category->addData([
                'level' => 1,
                'entity_id' => $category->getId(),
                'is_active' => $category->getEnabled(),
                'parent' => 0
            ]);

            // to obtain truncated category name
            /** @var $block Messages */
            $block = $this->layoutFactory->create()->getMessagesBlock();
            $block->setMessages($this->messageManager->getMessages(true));

            /** @var Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();

            return $resultJson->setData(
                [
                    'messages' => $block->getGroupedHtml(),
                    'error' => $hasError,
                    'category' => $category->toArray()
                ]
            );
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPost('category')) {
            $category = $this->initCategory();

            if (!$category) {
                $resultRedirect->setPath('magelearn_productsgrid/*/', ['_current' => true]);

                return $resultRedirect;
            }

            $category->addData($data);
            if ($posts = $this->getRequest()->getPost('posts', false)) {
                $category->setPostsData($this->jsHelper->decodeGridSerializedInput($posts));
            }

            $this->_eventManager->dispatch(
                'magelearn_productsgrid_category_prepare_save',
                ['category' => $category, 'request' => $this->getRequest()]
            );

            try {
                $category->save();
                $this->messageManager->addSuccessMessage(__('You saved the Category.'));
                $this->_getSession()->setData('magelearn_productsgrid_category_data', false);
                
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('magelearn_productsgrid/*/edit', ['id' => $category->getId(), '_current' => true]);
                } else {
                    $resultRedirect->setPath('magelearn_productsgrid/*/');
                }
                return $resultRedirect;
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_getSession()->setData('magelearn_productsgrid_category_data', $data);
            }

            $resultRedirect->setPath('magelearn_productsgrid/*/edit', ['_current' => true, 'id' => $category->getId()]);

            return $resultRedirect;
        }

        $resultRedirect->setPath('magelearn_productsgrid/*/edit', ['_current' => true]);

        return $resultRedirect;
    }
}
