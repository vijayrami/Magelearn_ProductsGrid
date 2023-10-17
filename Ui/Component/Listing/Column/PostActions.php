<?php

namespace Magelearn\ProductsGrid\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Actions
 * @package Magelearn\ProductsGrid\Ui\Component\Listing\Columns
 */
class PostActions extends Column
{
    const URL_PATH_EDIT = 'magelearn_productsgrid/post/edit';
    const URL_PATH_DELETE = 'magelearn_productsgrid/post/delete';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = [
                    'edit' => [
                        'href' => $this->urlBuilder->getUrl(
                            static::URL_PATH_EDIT,
                            [
                                'id' => $item['item_id']
                            ]
                            ),
                        'label' => __('Edit')
                    ],
                    'delete' => [
                        'href' => $this->urlBuilder->getUrl(
                            static::URL_PATH_DELETE,
                            [
                                'id' => $item['item_id']
                            ]
                            ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete %1',$item['item_id']),
                            'message' => __('Are you sure you wan\'t to delete a %1 record ?',$item['item_id'])
                        ]
                    ]
                ];
            }
        }

        return $dataSource;
    }
}
