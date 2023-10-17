<?php
namespace Magelearn\ProductsGrid\Helper;

class SwatchMedia extends \Magento\Swatches\Helper\Media
{
    const SWATCH_MEDIA_PATH = 'attribute/swatch';
    const SWATCH_MAIN_IMAGE = 'swatch_image';

    /**
     * @var array
     */
    protected $swatchImageTypes = [self::SWATCH_MAIN_IMAGE, 'swatch_thumb'];

    /**
     * Media swatch path
     *
     * @return string
     */
    public function getSwatchMediaPath()
    {
        return static::SWATCH_MEDIA_PATH;
    }

    /**
     * Media path with swatch_image or swatch_thumb folder
     *
     * @param string $swatchType
     * @return string
     */
    public function getSwatchCachePath($swatchType)
    {
        return $this->getSwatchMediaPath() . '/' . $swatchType . '/';
    }
}
