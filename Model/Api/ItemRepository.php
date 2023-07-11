<?php

namespace Magelearn\ProductsGrid\Model\Api;

use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection as SalesAbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magelearn\ProductsGrid\Api\ItemRepositoryInterface;
use Magelearn\ProductsGrid\Api\Data\CategoryInterface;
use Magelearn\ProductsGrid\Api\Data\PostInterface;
use Magelearn\ProductsGrid\Api\Data\TagInterface;
use Magelearn\ProductsGrid\Helper\Data;

/**
 * Class ItemRepository
 * @package Magelearn\ProductsGrid\Model\Api
 */
class ItemRepository implements ItemRepositoryInterface
{
    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepositoryInterface;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * ItemRepository constructor.
     *
     * @param Data $helperData
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param CollectionProcessorInterface $collectionProcessor
     * @param RequestInterface $request
     * @param DateTime $date
     */
    public function __construct(
        Data $helperData,
        CustomerRepositoryInterface $customerRepositoryInterface,
        CollectionProcessorInterface $collectionProcessor,
        RequestInterface $request,
        DateTime $date
    ) {
        $this->_request                     = $request;
        $this->_helperData                  = $helperData;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->date                         = $date;
        $this->collectionProcessor          = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function getAllPost()
    {
        $collection = $this->_helperData->getFactoryByType()->create()->getCollection();

        return $this->getAllItem($collection);
    }

    /**
     * @inheritDoc
     */
    public function getPostByTagName($tagName)
    {
        $tag = $this->_helperData->getFactoryByType('tag')->create()->getCollection()
            ->addFieldToFilter('name', $tagName)->getFirstItem();

        return $tag->getSelectedPostsCollection()->getItems();
    }

    /**
     * @inheritDoc
     */
    public function getProductByPost($postId)
    {
        $post = $this->_helperData->getFactoryByType()->create()->load($postId);

        return $post->getSelectedProductsCollection()->getItems();
    }

    /**
     * @inheritDoc
     */
    public function getPostList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->_helperData->getPostCollection();

        return $this->getListEntity($collection, $searchCriteria);
    }

    /**
     * @param PostInterface $post
     *
     * @return PostInterface
     */
    public function createPost($post)
    {
        $data = $post->getData();

        if ($this->checkPostData($data)) {
            $this->prepareData($data);
            $post->addData($data);
            $post->save();
        }

        return $post;
    }

    /**
     * @param string $postId
     *
     * @return string|null
     * @throws Exception
     */
    public function deletePost($postId)
    {
        $post = $this->_helperData->getFactoryByType()->create()->load($postId);

        if ($post) {
            $post->delete();

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function updatePost($postId, $post)
    {
        if (empty($postId)) {
            throw new InputException(__('Invalid post id %1', $postId));
        }
        $subPost = $this->_helperData->getFactoryByType()->create()->load($postId);

        if (!$subPost->getId()) {
            throw new NoSuchEntityException(
                __(
                    'The "%1" Post doesn\'t exist.',
                    $postId
                )
            );
        }

        $subPost->addData($post->getData())->save();

        return $subPost;
    }

    /**
     * @inheritDoc
     */
    public function getAllTag()
    {
        $collection = $this->_helperData->getFactoryByType('tag')->create()->getCollection();

        return $this->getAllItem($collection);
    }

    /**
     * @inheritDoc
     */
    public function getTagList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->_helperData->getFactoryByType('tag')->create()->getCollection();

        return $this->getListEntity($collection, $searchCriteria);
    }

    /**
     * @param TagInterface $tag
     *
     * @return TagInterface
     */
    public function createTag($tag)
    {
        if (!empty($tag->getName())) {
            if (empty($tag->getEnabled())) {
                $tag->setEnabled(1);
            }
            if (empty($tag->getCreatedAt())) {
                $tag->setCreatedAt($this->date->date());
            }
            $tag->save();
        }

        return $tag;
    }

    /**
     * @param string $tagId
     *
     * @return bool|string
     * @throws Exception
     */
    public function deleteTag($tagId)
    {
        $tag = $this->_helperData->getFactoryByType('tag')->create()->load($tagId);

        if ($tag) {
            $tag->delete();

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function updateTag($tagId, $tag)
    {
        if (empty($tagId)) {
            throw new InputException(__('Invalid tag id %1', $tagId));
        }
        $subTag = $this->_helperData->getFactoryByType('tag')->create()->load($tagId);

        if (!$subTag->getId()) {
            throw new NoSuchEntityException(
                __(
                    'The "%1" Tag doesn\'t exist.',
                    $tagId
                )
            );
        }

        $subTag->addData($tag->getData())->save();

        return $subTag;
    }
    
    /**
     * @inheritDoc
     */
    public function getAllCategory()
    {
        $collection = $this->_helperData->getFactoryByType('category')->create()->getCollection();
        
        return $this->getAllItem($collection);
    }

    /**
     * @inheritDoc
     */
    public function getCategoryList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->_helperData->getFactoryByType('category')->create()->getCollection();

        return $this->getListEntity($collection, $searchCriteria);
    }

    /**
     * @inheritDoc
     */
    public function getPostsByCategoryId($categoryId)
    {
        $category = $this->_helperData->getFactoryByType('category')->create()->load($categoryId);

        return $category->getSelectedPostsCollection()->getItems();
    }

    /**
     * @inheritDoc
     */
    public function getCategoriesByPostId($postId)
    {
        $post = $this->_helperData->getFactoryByType()->create()->load($postId);

        return $post->getSelectedCategoriesCollection()->getItems();
    }

    /**
     * @param CategoryInterface $category
     *
     * @return CategoryInterface
     */
    public function createCategory($category)
    {
        if (!empty($category->getName())) {
            if (empty($category->getEnabled())) {
                $category->setEnabled(1);
            }
            if (empty($category->getCreatedAt())) {
                $category->setCreatedAt($this->date->date());
            }
            $category->save();
        }

        return $category;
    }

    /**
     * @param string $categoryId
     *
     * @return bool|string
     * @throws Exception
     */
    public function deleteCategory($categoryId)
    {
        $category = $this->_helperData->getFactoryByType('category')->create()->load($categoryId);

        if ($category) {
            $category->delete();

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function updateCategory($categoryId, $category)
    {
        if (empty($categoryId)) {
            throw new InputException(__('Invalid category id %1', $categoryId));
        }
        $subCategory = $this->_helperData->getFactoryByType('category')->create()->load($categoryId);

        if (!$subCategory->getId()) {
            throw new NoSuchEntityException(
                __(
                    'The "%1" Category doesn\'t exist.',
                    $categoryId
                )
            );
        }

        $subCategory->addData($category->getData())->save();

        return $subCategory;
    }

    /**
     * @param array $data
     */
    protected function prepareData(&$data)
    {
        if (!empty($data['categories_ids'])) {
            $data['categories_ids'] = explode(',', $data['categories_ids']);
        }
        if (!empty($data['tags_ids'])) {
            $data['tags_ids'] = explode(',', $data['tags_ids']);
        }
        if (empty($data['enabled'])) {
            $data['enabled'] = 0;
        }
        if (empty($data['store_ids'])) {
            $data['store_ids'] = 0;
        }
        $data['created_at'] = $this->date->date();
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    protected function checkPostData($data)
    {
        if (empty($data['name'])) {
            return false;
        }

        if (!empty($data['categories_ids'])) {
            $collection = $this->_helperData->getFactoryByType('category')->create()->getCollection();
            foreach (explode(',', $data['categories_ids']) as $id) {
                if ($collection->addFieldToFilter('category_id', $id)->count() < 1) {
                    return false;
                }
            }
        }

        if (!empty($data['tags_ids'])) {
            $collection = $this->_helperData->getFactoryByType('tag')->create()->getCollection();
            foreach (explode(',', $data['tags_ids']) as $id) {
                if ($collection->addFieldToFilter('tag_id', $id)->count() < 1) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param SalesAbstractCollection|AbstractCollection $searchResult
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return mixed
     */
    protected function getListEntity($searchResult, $searchCriteria)
    {
        $this->collectionProcessor->process($searchCriteria, $searchResult);
        $searchResult->setSearchCriteria($searchCriteria);

        return $searchResult;
    }

    /**
     * @param AbstractCollection $collection
     *
     * @return mixed
     */
    protected function getAllItem($collection)
    {
        $page  = $this->_request->getParam('page', 1);
        $limit = $this->_request->getParam('limit', 10);

        $collection->getSelect()->limitPage($page, $limit);

        return $collection->getItems();
    }
}
