<?php

declare(strict_types=1);

namespace Magelearn\ProductsGrid\Api\Data;

interface SwatchInterface
{
    const SWATCH_ID = 'swatch_id';
    const POSITION = 'position';
    const DESCRIPTION = 'description';
    const ITEM_ID = 'item_id';
    const IMAGE = 'image';

    /**
     * Get swatch_id
     * @return int|null
     */
    public function getSwatchId();

    /**
     * Set swatch_id
     * @param int $swatchId
     * @return \Magelearn\ProductsGrid\Api\Data\SwatchInterface
     */
    public function setSwatchId(int $swatchId): SwatchInterface;

    /**
     * Get position
     * @return int
     */
    public function getPosition(): int;

    /**
     * Set position
     * @param int $position
     * @return \Magelearn\ProductsGrid\Api\Data\SwatchInterface
     */
    public function setPosition(int $position): SwatchInterface;

    /**
     * Get description
     * @return string|NULL
     */
    public function getDescription(): ?string;

    /**
     * Set description
     * @param string $description
     * @return \Magelearn\ProductsGrid\Api\Data\SwatchInterface
     */
    public function setDescription(?string $description): SwatchInterface;

    /**
     * Get Item id
     * @return int
     */
    public function getItemId(): int;

    /**
     * Set item id
     * @param int $itemId
     * @return \Magelearn\ProductsGrid\Api\Data\SwatchInterface
     */
    public function setItemId(int $itemId): SwatchInterface;

    /**
     * Get image
     * @return string|null
     */
    public function getImage();

    /**
     * Set image
     * @param string $image
     * @return \Magelearn\ProductsGrid\Api\Data\SwatchInterface
     */
    public function setImage(string $image): SwatchInterface;
}
