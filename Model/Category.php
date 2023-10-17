<?php

namespace Magelearn\ProductsGrid\Model;

use Exception;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magelearn\ProductsGrid\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magelearn\ProductsGrid\Model\ResourceModel\Post\Collection;
use Magelearn\ProductsGrid\Model\ResourceModel\Post\CollectionFactory;

/**
 * @method Category setName($name)
 * @method Category setEnabled($enabled)
 * @method mixed getName()
 * @method mixed getEnabled()
 * @method Category setCreatedAt(string $createdAt)
 * @method string getCreatedAt()
 * @method Category setUpdatedAt(string $updatedAt)
 * @method string getUpdatedAt()
 * @method Category setAffectedCategoryIds(array $ids)
 * @method Category setPostsData(array $data)
 * @method array getPostsData()
 * @method Category setIsChangedPostList(bool $flag)
 * @method bool getIsChangedPostList()
 * @method Category setAffectedPostIds(array $ids)
 * @method bool getAffectedPostIds()
 */
class Category extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'magelearn_item_category';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'magelearn_item_category';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'magelearn_item_category';

    /**
     * Post Collection
     *
     * @var Collection
     */
    public $postCollection;

    /**
     * Post Category Factory
     *
     * @var CategoryFactory
     */
    public $categoryFactory;

    /**
     * Post Collection Factory
     *
     * @var CollectionFactory
     */
    public $postCollectionFactory;

    /**
     * @var CategoryCollectionFactory
     */
    public $categoryCollectionFactory;

    /**
     * Category constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param CategoryFactory $categoryFactory
     * @param CollectionFactory $postCollectionFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CategoryFactory $categoryFactory,
        CollectionFactory $postCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Category::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
    
    /**
     * get entity default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];
        $values['enabled'] = '1';
        
        return $values;
    }

    /**
     * @return array|mixed
     */
    public function getPostsPosition()
    {
        if (!$this->getId()) {
            return [];
        }
        $array = $this->getData('posts_position');
        if ($array === null) {
            $array = $this->getResource()->getPostsPosition($this);
            $this->setData('posts_position', $array);
        }

        return $array;
    }

    /**
     * @return Collection
     */
    public function getSelectedPostsCollection()
    {
        if (!$this->postCollection) {
            $collection = $this->postCollectionFactory->create();
            $collection->join(
                ['cat' => $this->getResource()->getTable('magelearn_item_post_category')],
                'main_table.item_id=cat.item_id AND cat.category_id=' . $this->getId(),
                ['position']
            );
            $collection->setOrder('item_id','DESC');
            $this->postCollection = $collection;
        }

        return $this->postCollection;
    }
}
