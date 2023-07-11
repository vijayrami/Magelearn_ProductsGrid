<?php

namespace Magelearn\ProductsGrid\Api\Data;

/**
 * Interface TagInterface
 * @package Magelearn\ProductsGrid\Api\Data
 */
interface TagInterface
{
    /**
     * Constants used as data array keys
     */
    const TAG_ID           = 'tag_id';
    const NAME             = 'name';
    const DESCRIPTION      = 'description';
    const ENABLED          = 'enabled';
    const UPDATED_AT       = 'updated_at';
    const CREATED_AT       = 'created_at';

    const ATTRIBUTES = [
        self::TAG_ID,
        self::NAME,
        self::DESCRIPTION,
        self::ENABLED
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
     * Get Item Description
     *
     * @return string/null
     */
    public function getDescription();
    
    /**
     * Set Item Short Description
     *
     * @param string $content
     *
     * @return $this
     */
    public function setDescription($content);
    
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
}
