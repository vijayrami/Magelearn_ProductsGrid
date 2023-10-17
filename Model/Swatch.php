<?php

declare(strict_types=1);

namespace Magelearn\ProductsGrid\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magelearn\ProductsGrid\Api\Data\SwatchInterface;
use Magelearn\ProductsGrid\Model\ResourceModel\Post\Collection;
use Magelearn\ProductsGrid\Model\ResourceModel\Post\CollectionFactory;

class Swatch extends AbstractModel implements SwatchInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'magelearn_item_swatch';
    
    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'magelearn_item_swatch';
    /**
     * {@inheritdoc}
     */
    protected $_eventPrefix = 'magelearn_item_swatch';
    
    /**
     * Post Collection
     *
     * @var Collection
     */
    public $postCollection;
    
    /**
     * Post Collection Factory
     *
     * @var CollectionFactory
     */
    public $postCollectionFactory;
    
    /**
     * Swatch constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param CollectionFactory $postCollectionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CollectionFactory $postCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
        ) {
            $this->postCollectionFactory = $postCollectionFactory;
            
            parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magelearn\ProductsGrid\Model\ResourceModel\Swatch::class);
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
     * {@inheritdoc}
     */
    public function getSwatchId()
    {
        return $this->getData(self::SWATCH_ID);
    }
    
    /**
     * {@inheritdoc}
     */
    public function setSwatchId(int $swatchId): SwatchInterface
    {
        return $this->setData(self::SWATCH_ID, $swatchId);
    }
    
    /**
     * Get description
     * @return string|NULL
     */
    public function getDescription(): ?string
    {
        return $this->getData(self::DESCRIPTION);
    }
    
    /**
     * {@inheritdoc}
     */
    public function setDescription(?string $description): SwatchInterface
    {
        return $this->setData(self::DESCRIPTION, $description);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPosition(): int
    {
        return (int)$this->getData(self::POSITION);
    }
    
    /**
     * {@inheritdoc}
     */
    public function setPosition(int $position): SwatchInterface
    {
        return $this->setData(self::POSITION, $position);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getItemId(): int
    {
        return $this->getData(self::ITEM_ID);
    }
    
    /**
     * {@inheritdoc}
     */
    public function setItemId(int $itemId): SwatchInterface
    {
        return $this->setData(self::ITEM_ID, $itemId);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }
    
    /**
     * {@inheritdoc}
     */
    public function setImage(string $image): SwatchInterface
    {
        return $this->setData(self::IMAGE, $image);
    }
}
