<?php
declare(strict_types=1);
 
namespace Magelearn\ProductsGrid\Controller\Adminhtml\Post;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magelearn\ProductsGrid\Controller\Adminhtml\Post;
use Magelearn\ProductsGrid\Model\PostFactory;
 
class Delete extends Post
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
     * Delete constructor.
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
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->initPost();
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Post.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Post to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}