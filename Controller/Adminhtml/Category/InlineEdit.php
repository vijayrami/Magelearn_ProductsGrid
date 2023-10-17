<?php

namespace Magelearn\ProductsGrid\Controller\Adminhtml\Category;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magelearn\ProductsGrid\Model\Category;
use Magelearn\ProductsGrid\Model\CategoryFactory;
use RuntimeException;

/**
 * Class InlineEdit
 * @package Magelearn\ProductsGrid\Controller\Adminhtml\Category
 */
class InlineEdit extends Action
{
    /**
     * @var JsonFactory
     */
    public $jsonFactory;

    /**
     * @var CategoryFactory
     */
    public $categoryFactory;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        CategoryFactory $categoryFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->categoryFactory = $categoryFactory;

        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && !empty($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        $key = array_keys($postItems);
        $categoryId = !empty($key) ? (int)$key[0] : '';
        /** @var Category $category */
        $category = $this->categoryFactory->create()->load($categoryId);
        try {
            $category->addData($postItems[$categoryId])
                ->save();
        } catch (LocalizedException $e) {
            $messages[] = $this->getErrorWithCategoryId($category, $e->getMessage());
            $error = true;
        } catch (RuntimeException $e) {
            $messages[] = $this->getErrorWithCategoryId($category, $e->getMessage());
            $error = true;
        } catch (Exception $e) {
            $messages[] = $this->getErrorWithCategoryId($category, __('Something went wrong while saving the Category.'));
            $error = true;
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Category id to error message
     *
     * @param Category $category
     * @param string $errorText
     *
     * @return string
     */
    public function getErrorWithCategoryId(Category $category, $errorText)
    {
        return '[Category ID: ' . $category->getId() . '] ' . $errorText;
    }
}
