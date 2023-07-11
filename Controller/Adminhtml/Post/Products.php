<?php

namespace Magelearn\ProductsGrid\Controller\Adminhtml\Post;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Magelearn\ProductsGrid\Controller\Adminhtml\Post;
use Magelearn\ProductsGrid\Model\PostFactory;

/**
 * Class Products
 * @package Magelearn\ProductsGrid\Controller\Adminhtml\Post
 */
class Products extends Post
{
    /**
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * Products constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param PostFactory $productFactory
     * @param LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PostFactory $productFactory,
        LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($productFactory, $context, $coreRegistry);

        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $this->initPost(true);

        return $this->resultLayoutFactory->create();
    }
}
