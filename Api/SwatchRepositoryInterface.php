<?php
declare(strict_types=1);
namespace Magelearn\ProductsGrid\Api;

interface SwatchRepositoryInterface
{
    /**
     * Save Swatch
     * @param \Magelearn\ProductsGrid\Api\Data\SwatchInterface $swatch
     * @return \Magelearn\ProductsGrid\Api\Data\SwatchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Magelearn\ProductsGrid\Api\Data\SwatchInterface $swatch
    );

    /**
     * Retrieve Swatch
     * @param string $swatchId
     * @return \Magelearn\ProductsGrid\Api\Data\SwatchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($swatchId);

    /**
     * Retrieve Swatch
     * @param string $postId
     * @return \Magelearn\ProductsGrid\Model\ResourceModel\Step\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListByPostId($postId);

    /**
     * Delete Swatch
     * @param \Magelearn\ProductsGrid\Api\Data\SwatchInterface $swatch
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Magelearn\ProductsGrid\Api\Data\SwatchInterface $swatch
    );

    /**
     * Delete Swatch by ID
     * @param string $swatchId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($swatchId);
}
