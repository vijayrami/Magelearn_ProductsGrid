<?php

namespace Magelearn\ProductsGrid\Controller\Adminhtml\Tag;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Messages;
use Magento\Framework\View\LayoutFactory;
use Magelearn\ProductsGrid\Controller\Adminhtml\Tag;
use Magelearn\ProductsGrid\Model\TagFactory;
use Magelearn\ProductsGrid\Controller\Adminhtml\PostDataProcessor;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Class Save
 * @package Magelearn\ProductsGrid\Controller\Adminhtml\Tag
 */
class Save extends Tag
{
    /**
     * @var Js
     */
    public $jsHelper;

    /**
     * Layout Factory
     *
     * @var LayoutFactory
     */
    public $layoutFactory;

    /**
     * Result Json Factory
     *
     * @var JsonFactory
     */
    public $resultJsonFactory;
    
    /**
     * DataProcessor
     *
     * @var PostDataProcessor
     */
    public $dataProcessor;
    
    protected $dataPersistor;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Js $jsHelper
     * @param LayoutFactory $layoutFactory
     * @param JsonFactory $resultJsonFactory
     * @param PostDataProcessor $dataProcessor,
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param TagFactory $tagFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Js $jsHelper,
        LayoutFactory $layoutFactory,
        JsonFactory $resultJsonFactory,
        PostDataProcessor $dataProcessor,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        TagFactory $tagFactory
    ) {
        $this->jsHelper = $jsHelper;
        $this->layoutFactory = $layoutFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataProcessor = $dataProcessor;
        $this->dataPersistor = $dataPersistor;

        parent::__construct($context, $registry, $tagFactory);
    }

    /**
     * @return $this|ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->getPost('return_session_messages_only')) {
            $tag = $this->initTag();
            $tagPostData = $this->getRequest()->getPostValue();
            $tagPostData['enabled'] = 1;

            $tag->addData($tagPostData);

            try {
                if ($this->dataProcessor->validate($tagPostData, $validate = 'tag')) {
                    $tag->save();
                    $this->messageManager->addSuccessMessage(__('You saved the tag.'));
                }
            } catch (AlreadyExistsException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_objectManager->get(LoggerInterface::class)->critical($e);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_objectManager->get(LoggerInterface::class)->critical($e);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the tag.'));
                $this->_objectManager->get(LoggerInterface::class)->critical($e);
            }

            $hasError = (bool)$this->messageManager->getMessages()->getCountByType(
                MessageInterface::TYPE_ERROR
            );
            
            $tag->load($tag->getId());
            $tag->addData([
                'level' => 1,
                'entity_id' => $tag->getId(),
                'is_active' => $tag->getEnabled(),
                'parent' => 0
            ]);

            /** @var $block Messages */
            $block = $this->layoutFactory->create()->getMessagesBlock();
            $block->setMessages($this->messageManager->getMessages(true));

            /** @var Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();

            return $resultJson->setData([
                'messages' => $block->getGroupedHtml(),
                'error' => $hasError,
                'category' => $tag->toArray()
            ]);
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPost('tag');
        if ($data) {
            if (!$this->dataProcessor->validate($data, $validate = 'tag')) {
                $this->dataPersistor->set('magelearn_item_tag', $data);
                if (!empty($data['category_id'])) {
                    return $resultRedirect->setPath(
                        'magelearn_productsgrid/*/edit',
                        [
                            'id' => $data['tag_id'],
                            '_current' => true
                        ]
                        );
                } else {
                    return $resultRedirect->setPath(
                        'magelearn_productsgrid/*/edit',
                        [
                            '_current' => true
                        ]
                        );
                }
            }
            /** @var \Magelearn\ProductsGrid\Model\Tag $tag */
            $tag = $this->initTag();

            $tag->addData($data);
            if ($posts = $this->getRequest()->getPost('posts', false)) {
                $tag->setPostsData($this->jsHelper->decodeGridSerializedInput($posts));
            }

            $this->_eventManager->dispatch(
                'magelearn_item_tag_prepare_save',
                ['tag' => $tag, 'request' => $this->getRequest()]
            );

            try {
                $tag->save();

                $this->messageManager->addSuccessMessage(__('The Tag has been saved.'));
                $this->_session->setData('magelearn_item_tag_data', false);
                $this->dataPersistor->clear('magelearn_item_tag');

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('magelearn_productsgrid/*/edit', ['id' => $tag->getId(), '_current' => true]);
                } else {
                    $resultRedirect->setPath('magelearn_productsgrid/*/');
                }

                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Tag.'));
            }
            $this->_getSession()->setData('magelearn_item_tag_data', $data);
            $this->dataPersistor->set('magelearn_item_tag', $data);
            $resultRedirect->setPath('magelearn_productsgrid/*/edit', ['id' => $tag->getId(), '_current' => true]);
            return $resultRedirect;
        }

        $resultRedirect->setPath('magelearn_productsgrid/*/');

        return $resultRedirect;
    }
}
