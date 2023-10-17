<?php
namespace Magelearn\ProductsGrid\Block\Adminhtml\Post\Edit\Options;

use Magento\Backend\Block\Template\Context;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory;
use Magento\Framework\Registry;
use Magento\Framework\Validator\UniversalFactory;
use Magelearn\ProductsGrid\Helper\SwatchMedia;
use Magelearn\ProductsGrid\Helper\Data;
use Magelearn\ProductsGrid\Model\Post;
use Magelearn\ProductsGrid\Model\Swatch as SwatchModel;
use Magelearn\ProductsGrid\Model\SwatchRepository;
use Magelearn\ProductsGrid\Model\ResourceModel\Swatch\Collection as SwatchCollection;

class Swatch extends \Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\Options
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Magelearn_ProductsGrid::post/option/swatch.phtml';
    
    /**
     * @var SwatchMedia
     */
    private $swatchMediaHelper;
    
    /**
     * @var SwatchRepository
     */
    private $swatchRepository;
    
    /**
     * Swatch constructor.
     * @param SwatchMedia $swatchMediaHelper
     * @param SwatchRepository $swatchRepository
     * @param Context $context
     * @param Registry $registry
     * @param CollectionFactory $attrOptionCollectionFactory
     * @param UniversalFactory $universalFactory
     * @param array $data
     */
    public function __construct(
        SwatchMedia $swatchMediaHelper,
        SwatchRepository $swatchRepository,
        Context $context,
        Registry $registry,
        CollectionFactory $attrOptionCollectionFactory,
        UniversalFactory $universalFactory,
        array $data = []
        ) {
            parent::__construct($context, $registry, $attrOptionCollectionFactory, $universalFactory, $data);
            $this->swatchMediaHelper = $swatchMediaHelper;
            $this->swatchRepository = $swatchRepository;
    }
    
    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getValues()
    {
        $values = [];
        /** @var Post $post */
        $post = $this->_registry->registry('magelearn_item_post');
        if (!$post->getId()) {
            return $values;
        }
        $list = $this->swatchRepository->getListByPostId($post->getId());
        $swatchData = $list->getData();

        foreach ($swatchData as $swatch) {
            /** @var SwatchModel $swatch */
            $value = [
                'id' => $swatch['swatch_id'],
                'checked' => '',
                'intype' => 'radio',
                'description' => $swatch['description'],
                'sort_order' => $swatch['position'],
                'thumbnail' => null,
                'image' => null,
                'defaultswatch0' => ''
            ];
            $swatchImage = $swatch['image'];
            if (null !== $swatchImage && !empty($swatchImage)) {
                $value['thumbnail'] = $swatchImage;
                $value['image'] = $this->getImageStyle($swatchImage);
                unset($value['defaultswatch0']);
            }
            $values[] = $value;
        }
        return $values;
    }
    
    /**
     * @param string $image
     * @return string
     */
    protected function getImageStyle(string $image = '')
    {
        if (!empty($image)) {
            if ($image[0] == '#') {
                return 'background: '.$image;
            } elseif ($image[0] == '/') {
                $path = $this->swatchMediaHelper->getSwatchAttributeImage('swatch_image', $image);
                return 'background: url('. $path .'); background-size: cover;';
            }
        }
        return $image;
    }
    
    /**
     * @return false|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getJsConfig()
    {
        $data = [
            'attributesData' => $this->getValues(),
            'uploadActionUrl' => $this->getUrl('magelearn_productsgrid/swatch/show'),
            'isSortable' => 'true',
            'isReadOnly' => 'false'
        ];
        return json_encode($data);
    }
    
}
