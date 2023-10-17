<?php

namespace Magelearn\ProductsGrid\Helper;

use DateTimeZone;
use Exception;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magelearn\ProductsGrid\Model\Tag;
use Magelearn\ProductsGrid\Model\TagFactory;
use Magelearn\ProductsGrid\Model\Category;
use Magelearn\ProductsGrid\Model\CategoryFactory;
use Magelearn\ProductsGrid\Model\Post;
use Magelearn\ProductsGrid\Model\PostFactory;

/**
 * Class Data
 * @package Magelearn\ProductsGrid\Helper
 */
class Data extends AbstractHelper
{
    const TYPE_POST          = 'post';
    const TYPE_CATEGORY      = 'category';
    const TYPE_TAG           = 'tag';
    
    /**
     * @var PostFactory
     */
    public $postFactory;
    
    /**
     * @var CategoryFactory
     */
    public $categoryFactory;
    
    /**
     * @var TagFactory
     */
    public $tagFactory;

    /**
     * @var DateTime
     */
    public $dateTime;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param PostFactory $postFactory
     * @param CategoryFactory $categoryFactory
     * @param TagFactory $tagFactory
     * @param DateTime $dateTime
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        PostFactory $postFactory,
        CategoryFactory $categoryFactory,
        TagFactory $tagFactory,
        DateTime $dateTime
    ) {
        $this->postFactory        = $postFactory;
        $this->categoryFactory    = $categoryFactory;
        $this->tagFactory         = $tagFactory;
        $this->dateTime           = $dateTime;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @return Image
     */
    public function getImageHelper()
    {
        return $this->objectManager->get(Image::class);
    }
    
    /**
     * @param null $type
     * @param null $id
     * @param null $storeId
     *
     * @return PostCollection
     * @throws NoSuchEntityException
     */
    public function getPostCollection($type = null, $id = null, $storeId = null)
    {
        if ($id === null) {
            $id = $this->_request->getParam('id');
        }
        
        /** @var PostCollection $collection */
        $collection = $this->getPostList($storeId);
        
        switch ($type) {
            case self::TYPE_CATEGORY:
                $collection->join(
                ['category' => $collection->getTable('magelearn_item_post_category')],
                'main_table.item_id=category.item_id AND category.category_id=' . $id,
                ['position']
                );
                $collection->getSelect()->order('position asc');
                break;
            case self::TYPE_TAG:
                $collection->join(
                ['tag' => $collection->getTable('magelearn_item_post_tag')],
                'main_table.item_id=tag.item_id AND tag.tag_id=' . $id,
                ['position']
                );
                $collection->getSelect()->order('position asc');
                break;            
        }
        
        return $collection;
    }
    
    /**
     * @param null $storeId
     *
     * @return PostCollection
     * @throws NoSuchEntityException
     */
    public function getPostList($storeId = null)
    {
        /** @var PostCollection $collection */
        $collection = $this->getObjectList(self::TYPE_POST, $storeId)
        ->addFieldToFilter('created_at', ['to' => $this->dateTime->date()])
        ->setOrder('item_id', 'desc');
        
        return $collection;
    }
    
    /**
     * Get object collection (Category, Tag, Post)
     *
     * @param null $type
     * @param null $storeId
     *
     * @return CategoryCollection|PostCollection|TagCollection|Collection
     * @throws NoSuchEntityException
     */
    public function getObjectList($type = null, $storeId = null)
    {
        /** @var CategoryCollection|PostCollection|TagCollection|Collection $collection */
        $collection = $this->getFactoryByType($type)
        ->create()
        ->getCollection()
        ->addFieldToFilter('enabled', 1);
        
        $this->addStoreFilter($collection, $storeId);
        
        return $collection;
    }
    
    /**
     * @param $type
     *
     * @return CategoryFactory|PostFactory|TagFactory
     */
    public function getFactoryByType($type = null)
    {
        switch ($type) {
            case self::TYPE_CATEGORY:
                $object = $this->categoryFactory;
                break;
            case self::TYPE_TAG:
                $object = $this->tagFactory;
                break;
            default:
                $object = $this->postFactory;
        }
        
        return $object;
    }
}
