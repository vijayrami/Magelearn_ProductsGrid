<?php
declare(strict_types=1);
 
namespace Magelearn\ProductsGrid\Controller\Adminhtml\Tag;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magelearn\ProductsGrid\Controller\Adminhtml\Tag;
use Magelearn\ProductsGrid\Model\TagFactory;
 
class Delete extends Tag
{
    /**
     * Tag Factory
     *
     * @var TagFactory
     */
    public $tagFactory;
    
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
     * @param TagFactory $tagFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        TagFactory $tagFactory,
        PageFactory $resultPageFactory
        ) {
            $this->resultPageFactory = $resultPageFactory;
            
            parent::__construct($context, $coreRegistry, $tagFactory);
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
                $model = $this->initTag();
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Tag.'));
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
        $this->messageManager->addErrorMessage(__('We can\'t find a Tag to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}