<?php

namespace Magelearn\ProductsGrid\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;
use Magelearn\ProductsGrid\Controller\Adminhtml\Category;
use Magelearn\ProductsGrid\Model\CategoryFactory;

/**
 * Class Posts
 * @package Magelearn\ProductsGrid\Controller\Adminhtml\Category
 */
class Posts extends Category
{
    /**
     * Result layout factory
     *
     * @var LayoutFactory
     */
    public $resultLayoutFactory;

    /**
     * Posts constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param CategoryFactory $categoryFactory
     * @param LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        CategoryFactory $categoryFactory,
        LayoutFactory $resultLayoutFactory
    ) {
        $this->resultLayoutFactory = $resultLayoutFactory;

        parent::__construct($context, $coreRegistry, $categoryFactory);
    }

    /**
     * @return Layout
     */
    public function execute()
    {
        $this->initCategory(true);

        return $this->resultLayoutFactory->create();
    }
}
