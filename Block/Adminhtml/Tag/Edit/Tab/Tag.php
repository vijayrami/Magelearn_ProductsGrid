<?php

namespace Magelearn\ProductsGrid\Block\Adminhtml\Tag\Edit\Tab;

use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * Class Tag
 * @package Magelearn\ProductsGrid\Block\Adminhtml\Tag\Edit\Tab
 */
class Tag extends Generic implements TabInterface
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
     * @var Enabledisable
     */
    protected $enableDisable;

    /**
     * Tag constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Yesno $booleanOptions
     * @param Enabledisable $enableDisable
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Yesno $booleanOptions,
        Enabledisable $enableDisable,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        $this->booleanOptions = $booleanOptions;
        $this->enableDisable = $enableDisable;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var \Magelearn\ProductsGrid\Model\Tag $tag */
        $tag = $this->_coreRegistry->registry('magelearn_item_tag');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('tag_');
        $form->setFieldNameSuffix('tag');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Tag Information'),
            'class' => 'fieldset-wide'
        ]);
        if ($tag->getId()) {
            $fieldset->addField('tag_id', 'hidden', ['name' => 'tag_id']);
        }

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => __('Name'),
            'title' => __('Name'),
            'required' => true,
        ]);
        
        $fieldset->addField('enabled', 'select', [
            'name' => 'enabled',
            'label' => __('Status'),
            'title' => __('Status'),
            'values' => $this->enableDisable->toOptionArray(),
        ]);
        if (!$tag->hasData('enabled')) {
            $tag->setEnabled(1);
        }
        
        $fieldset->addField('description', 'editor', [
            'name' => 'description',
            'label' => __('Description'),
            'title' => __('Description'),
            'config' => $this->wysiwygConfig->getConfig(['add_variables' => false, 'add_widgets' => false])
        ]);

        $form->addValues($tag->getData());
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
        return __('Tag');
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
