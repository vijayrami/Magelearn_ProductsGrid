<?php

namespace Magelearn\ProductsGrid\Block\Adminhtml\Post\Edit\Tab;

use DateTimeZone;
use Exception;
use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\System\Store;
use Magelearn\ProductsGrid\Block\Adminhtml\Post\Edit\Tab\Renderer\Category;
use Magelearn\ProductsGrid\Block\Adminhtml\Post\Edit\Tab\Renderer\Tag;
use Magelearn\ProductsGrid\Block\Adminhtml\Post\Edit\Options\Swatch;
use Magelearn\ProductsGrid\Helper\Image;
use Magelearn\ProductsGrid\Model\Config\Source\PostStatus;

/**
 * Class Post
 * @package Magelearn\ProductsGrid\Block\Adminhtml\Post\Edit\Tab
 */
class Post extends Generic implements TabInterface
{
    /**
     * Wysiwyg config
     *
     * @var Config
     */
    public $wysiwygConfig;

    /**
     * Country options
     *
     * @var Yesno
     */
    public $booleanOptions;

    /**
     * @var Store
     */
    public $systemStore;

    /**
     * @var Image
     */
    protected $imageHelper;
    
    /**
     * @var PostStatus
     */
    protected $_status;

    /**
     * @var DateTime
     */
    protected $_date;

    /**
     * Post constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param DateTime $dateTime
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Yesno $booleanOptions
     * @param Store $systemStore
     * @param Image $imageHelper
     * @param PostStatus $status
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DateTime $dateTime,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Yesno $booleanOptions,
        Store $systemStore,
        Image $imageHelper,
        PostStatus $status,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        $this->booleanOptions = $booleanOptions;
        $this->systemStore = $systemStore;
        $this->_date = $dateTime;
        $this->imageHelper = $imageHelper;
        $this->_status = $status;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function _prepareForm()
    {
        /** @var \Magelearn\ProductsGrid\Model\Post $post */
        $post = $this->_coreRegistry->registry('magelearn_item_post');

        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('post_');
        $form->setFieldNameSuffix('post');
        
        if ($post->getId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Post Information'), 'class' => 'fieldset-wide']
                );
            $fieldset->addField('item_id', 'hidden', ['name' => 'item_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add New Post'), 'class' => 'fieldset-wide']
                );
        }

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => __('Name'),
            'title' => __('Name'),
            'required' => true
        ]);
        
        $fieldset->addField('enabled', 'select', [
            'name' => 'enabled',
            'label' => __('Status'),
            'title' => __('Status'),
            'values' => $this->_status->toOptionArray()
        ]);
        if (!$post->hasData('enabled')) {
            $post->setEnabled(1);
        }

        $fieldset->addField('short_description', 'textarea', [
            'name' => 'short_description',
            'label' => __('Short Description'),
            'title' => __('Short Description')
        ]);
        $fieldset->addField('item_content', 'editor', [
            'name' => 'item_content',
            'label' => __('Content'),
            'title' => __('Content'),
            'config' => $this->wysiwygConfig->getConfig([
                'add_variables' => false,
                'add_widgets' => true,
                'add_directives' => true
            ])
        ]);

        if ($this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField('store_ids', 'hidden', [
                'name' => 'store_ids',
                'value' => $this->_storeManager->getStore()->getId()
            ]);
        } else {
            /** @var RendererInterface $rendererBlock */
            $rendererBlock = $this->getLayout()->createBlock(Element::class);
            $fieldset->addField('store_ids', 'multiselect', [
                'name' => 'store_ids',
                'label' => __('Store Views'),
                'title' => __('Store Views'),
                'values' => $this->systemStore->getStoreValuesForForm(false, true)
            ])->setRenderer($rendererBlock);

            if (!$post->hasData('store_ids')) {
                $post->setStoreIds(0);
            }
        }

        $fieldset->addField('image', \Magelearn\ProductsGrid\Block\Adminhtml\Renderer\Image::class, [
            'name' => 'image',
            'label' => __('Image'),
            'title' => __('Image'),
            'path' => $this->imageHelper->getBaseMediaPath(Image::TEMPLATE_MEDIA_TYPE_POST),
            'note' => __('The appropriate size is 265px * 250px.')
        ]);

        $fieldset->addField('categories_ids', Category::class, [
            'name' => 'categories_ids',
            'label' => __('Categories'),
            'title' => __('Categories'),
        ]);
        if (!$post->getCategoriesIds()) {
            $post->setCategoriesIds($post->getCategoryIds());
        }

        $fieldset->addField('tags_ids', Tag::class, [
            'name' => 'tags_ids',
            'label' => __('Tags'),
            'title' => __('Tags'),
        ]);
        if (!$post->getTagsIds()) {
            $post->setTagsIds($post->getTagIds());
        }
        
        $fieldset = $form->addFieldset(
            'itemconfiguration_fieldset',
            [
                'legend' => __('Post Configuration'),
                'class' => 'fieldset-wide'
            ]
            );
        
        $fieldset->addField(
            'item_title_color',
            'text',
            [
                'name' => 'item_title_color',
                'label' => __('Choose title color'),
                'title' => __('Choose title color'),
                'class' => 'jscolor {hash:true,refine:false}',
            ]
            );

        $form->addValues($post->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Post');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
