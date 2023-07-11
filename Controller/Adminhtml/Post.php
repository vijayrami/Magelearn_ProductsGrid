<?php
declare(strict_types=1);

namespace Magelearn\ProductsGrid\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magelearn\ProductsGrid\Model\PostFactory;

abstract class Post extends Action
{
    
    const ADMIN_RESOURCE = 'Magelearn_ProductsGrid::item';
    
    /**
     * Post Factory
     *
     * @var PostFactory
     */
    public $postFactory;
    
    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;
    
    /**
     * @param PostFactory $postFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        PostFactory $postFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
        ) {
            $this->postFactory = $postFactory;
            $this->coreRegistry = $coreRegistry;
            parent::__construct($context);
    }

    /**
     * @param bool $register
     * @param bool $isSave
     *
     * @return bool|\Magelearn\ProductsGrid\Model\Post
     */
    protected function initPost($register = false, $isSave = false)
    {
        $postId = (int)$this->getRequest()->getParam('id');
        
        /** @var \Magelearn\ProductsGrid\Model\Post $post */
        $post = $this->postFactory->create();
        if ($postId) {
            if (!$isSave) {
                $post->load($postId);
                if (!$post->getId()) {
                    $this->messageManager->addErrorMessage(__('This post no longer exists.'));
                    
                    return false;
                }
            }
        }
        
        if ($register) {
            $this->coreRegistry->register('magelearn_item_post', $post);
        }
        
        return $post;
    }
}