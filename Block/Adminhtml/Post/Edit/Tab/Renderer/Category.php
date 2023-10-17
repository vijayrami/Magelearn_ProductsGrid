<?php

namespace Magelearn\ProductsGrid\Block\Adminhtml\Post\Edit\Tab\Renderer;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Multiselect;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magelearn\ProductsGrid\Model\ResourceModel\Category\Collection;
use Magelearn\ProductsGrid\Model\ResourceModel\Category\CollectionFactory as PostCategoryCollectionFactory;

/**
 * Class Category
 * @package Magelearn\ProductsGrid\Block\Adminhtml\Post\Edit\Tab\Renderer
 */
class Category extends Multiselect
{
    /**
     * @var PostCategoryCollectionFactory
     */
    public $collectionFactory;

    /**
     * @var AuthorizationInterface
     */
    public $authorization;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Category constructor.
     *
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param PostCategoryCollectionFactory $collectionFactory
     * @param AuthorizationInterface $authorization
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        PostCategoryCollectionFactory $collectionFactory,
        AuthorizationInterface $authorization,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->authorization = $authorization;
        $this->_urlBuilder = $urlBuilder;

        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * @inheritdoc
     */
    public function getElementHtml()
    {
        $html = '<div class="admin__field-control admin__control-grouped">';
        $html .= '<div id="post-category-select" class="admin__field"
                    data-bind="scope:\'postCategory\'" data-index="index">';
        $html .= '<!-- ko foreach: elems() -->';
        $html .= '<input name="post[categories_ids]" data-bind="value: value" style="display: none"/>';
        $html .= '<!-- ko template: elementTmpl --><!-- /ko -->';
        $html .= '<!-- /ko -->';
        $html .= '</div>';

        $html .= '<div class="admin__field admin__field-group-additional admin__field-small"
                  data-bind="scope:\'create_category_button\'">';
        $html .= '<div class="admin__field-control">';
        $html .= '<!-- ko template: elementTmpl --><!-- /ko -->';
        $html .= '</div></div></div>';

        $html .= '<!-- ko scope: \'create_category_modal\' -->
        <!-- ko template: getTemplate() --><!-- /ko --><!-- /ko -->';

        $html .= $this->getAfterElementHtml();

        return $html;
    }

    /**
     * Get no display
     *
     * @return bool
     */
    public function getNoDisplay()
    {
        $isNotAllowed = !$this->authorization->isAllowed('Magelearn_ProductsGrid::category');

        return $this->getData('no_display') || $isNotAllowed;
    }
    
    /**
     * @return mixed
     */
    public function getCategoriesCollection()
    {
        /* @var $collection Collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('enabled', 1);
        $categoryById = [];
        foreach ($collection as $category) {
            $categoryById[$category->getId()]['value'] = $category->getId();
            $categoryById[$category->getId()]['is_active'] = 1;
            $categoryById[$category->getId()]['label'] = $category->getName();
        }
        
        return $categoryById;
    }

    /**
     * Get values for select
     *
     * @return array
     */
    public function getValues()
    {
        $values = $this->getValue();

        if (!is_array($values)) {
            $values = explode(',', $values);
        }

        if (!count($values)) {
            return [];
        }

        /* @var $collection Collection */
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('enabled', 1)
            ->addIdFilter($values);

        $options = [];
        foreach ($collection as $category) {
            $options[] = $category->getId();
        }

        return $options;
    }

    /**
     * Attach Post Category suggest widget initialization
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        $html = '<script type="text/x-magento-init">
            {
                "*": {
                    "Magento_Ui/js/core/app": {
                        "components": {
                            "postCategory": {
                                "component": "uiComponent",
                                "children": {
                                    "post_select_category": {
                                        "component": "Magelearn_ProductsGrid/js/components/new-category",
                                        "config": {
                                            "filterOptions": true,
                                            "disableLabel": true,
                                            "chipsEnabled": true,
                                            "levelsVisibility": "1",
                                            "elementTmpl": "ui/grid/filters/elements/ui-select",
                                            "options": ' . json_encode($this->getCategoriesCollection()) . ',
                                            "value": ' . json_encode($this->getValues()) . ',
                                            "listens": {
                                                "index=create_category:responseData": "setParsed",
                                                "newOption": "toggleOptionSelected"
                                            },
                                            "config": {
                                                "dataScope": "post_select_category",
                                                "sortOrder": 10
                                            }
                                        }
                                    }
                                }
                            },
                            "create_category_button": {
                                "title": "' . __('New Category') . '",
                                "formElement": "container",
                                "additionalClasses": "admin__field-small",
                                "componentType": "container",
                                "component": "Magento_Ui/js/form/components/button",
                                "template": "ui/form/components/button/container",
                                "actions": [
                                    {
                                        "targetName": "create_category_modal",
                                        "actionName": "toggleModal"
                                    },
                                    {
                                        "targetName": "create_category_modal.create_category",
                                        "actionName": "render"
                                    },
                                    {
                                        "targetName": "create_category_modal.create_category",
                                        "actionName": "resetForm"
                                    }
                                ],
                                "additionalForGroup": true,
                                "provider": false,
                                "source": "product_details",
                                "displayArea": "insideGroup"
                            },
                            "create_category_modal": {
                                "config": {
                                    "isTemplate": false,
                                    "componentType": "container",
                                    "component": "Magento_Ui/js/modal/modal-component",
                                    "options": {
                                        "title": "' . __('New Category') . '",
                                        "type": "slide"
                                    },
                                    "imports": {
                                        "state": "!index=create_category:responseStatus"
                                    }
                                },
                                "children": {
                                    "create_category": {
                                        "label": "",
                                        "componentType": "container",
                                        "component": "Magento_Ui/js/form/components/insert-form",
                                        "dataScope": "",
                                        "update_url": "' . $this->_urlBuilder->getUrl('mui/index/render') . '",
                                        "render_url": "' .
            $this->_urlBuilder->getUrl(
                'mui/index/render_handle',
                ['handle' => 'magelearn_productsgrid_category_create', 'buttons' => 1]
            ) . '",
                                        "autoRender": false,
                                        "ns": "item_new_category_form",
                                        "externalProvider": "item_new_category_form.new_category_form_data_source",
                                        "toolbarContainer": "${ $.parentName }",
                                        "formSubmitType": "ajax"
                                    }
                                }
                            }
                        }
                    }
                }
            }
        </script>';

        return $html;
    }
}
