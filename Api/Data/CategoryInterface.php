<?php

namespace Magelearn\ProductsGrid\Api\Data;

/**
 * Interface CategoryInterface
 * @package Magelearn\ProductsGrid\Api\Data
 */
interface CategoryInterface
{
    /**
     * Constants used as data array keys
     */
    const CATEGORY_ID      = 'category_id';
    const NAME             = 'name';
    const ENABLED          = 'enabled';
    const UPDATED_AT       = 'updated_at';
    const CREATED_AT       = 'created_at';

    const ATTRIBUTES = [
        self::CATEGORY_ID,
        self::NAME,
        self::ENABLED
    ];

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * @return string/null
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);
    
    /**
     * Get Cateory Enabled
     *
     * @return int/null
     */
    public function getEnabled();
    
    /**
     * Set Cateory Enabled
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
     * Get Post updated date
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set Post updated date
     *
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt);
}
