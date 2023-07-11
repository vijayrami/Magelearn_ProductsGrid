<?php

namespace Magelearn\ProductsGrid\Api\Data;

/**
 * Interface PostInterface
 * @package Magelearn\ProductsGrid\Api\Data
 */
interface PostInterface
{
    /**
     * Constants used as data array keys
     */
    const ITEM_ID           = 'item_id';
    const NAME              = 'name';
    const SHORT_DESCRIPTION = 'short_description';
    const ITEM_CONTENT      = 'item_content';
    const STORE_IDS         = 'store_ids';
    const IMAGE             = 'image';
    const ENABLED           = 'enabled';
    const UPDATED_AT        = 'updated_at';
    const CREATED_AT        = 'created_at';
    const CATEGORY_IDS      = 'category_ids';
    const TAG_IDS           = 'tag_ids';
    const ITEM_TITLE_COLOR  = 'item_title_color';

    const ATTRIBUTES = [
        self::ITEM_ID,
        self::NAME,
        self::SHORT_DESCRIPTION,
        self::ITEM_CONTENT,
        self::STORE_IDS,
        self::IMAGE,
        self::ENABLED,
        self::CATEGORY_IDS,
        self::TAG_IDS,
        self::ITEM_TITLE_COLOR
    ];

    /**
     * Get Item id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set Item id
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Get Item Name
     *
     * @return string/null
     */
    public function getName();

    /**
     * Set Item Name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Get Item Short Description
     *
     * @return string/null
     */
    public function getShortDescription();

    /**
     * Set Item Short Description
     *
     * @param string $content
     *
     * @return $this
     */
    public function setShortDescription($content);

    /**
     * Get Item Content
     *
     * @return string/null
     */
    public function getItemContent();

    /**
     * Set Item Content
     *
     * @param string $content
     *
     * @return $this
     */
    public function setItemContent($content);

    /**
     * Get Item Store Id
     *
     * @return int/null
     */
    public function getStoreIds();

    /**
     * Set Item Store Id
     *
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreIds($storeId);

    /**
     * Get Item Image
     *
     * @return string/null
     */
    public function getImage();

    /**
     * Set Item Image
     *
     * @param string $content
     *
     * @return $this
     */
    public function setImage($content);

    /**
     * Get Item Enabled
     *
     * @return int/null
     */
    public function getEnabled();

    /**
     * Set Item Enabled
     *
     * @param int $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled);

    /**
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get Item updated date
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set Item updated date
     *
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return int[]|null
     */
    public function getCategoryIds();

    /**
     * @param int[] $array
     *
     * @return $this
     */
    public function setCategoryIds($array);

    /**
     * @return int[]|null
     */
    public function getTagIds();

    /**
     * @param int[] $array
     *
     * @return $this
     */
    public function setTagIds($array);
    
    /**
     * Get Item Title Color
     *
     * @return string/null
     */
    public function getItemTitleColor();
    
    /**
     * Set Item Title Color
     *
     * @param string $itemTitleColor
     *
     * @return $this
     */
    public function setItemTitleColor($itemTitleColor);
}
