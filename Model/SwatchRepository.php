<?php
declare(strict_types=1);
namespace Magelearn\ProductsGrid\Model;

use Magelearn\ProductsGrid\Api\Data\SwatchInterface;
use Magelearn\ProductsGrid\Api\Data\SwatchInterfaceFactory;
use Magelearn\ProductsGrid\Api\SwatchRepositoryInterface;
use Magelearn\ProductsGrid\Api\Data\SwatchSearchResultsInterfaceFactory;
use Magelearn\ProductsGrid\Model\ResourceModel\Swatch as SwatchResource;
use Magelearn\ProductsGrid\Model\ResourceModel\Swatch\CollectionFactory as SwatchCollectionFactory;

use Magento\Store\Model\StoreManagerInterface;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;

class SwatchRepository implements SwatchRepositoryInterface
{
    /**
     * @var SwatchResource
     */
    protected $resource;

    /**
     * @var SwatchInterfaceFactory
     */
    protected $swatchFactory;

    /**
     * @var SwatchCollectionFactory
     */
    protected $swatchCollectionFactory;

    /**
     * @var SwatchSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param SwatchResource $resource
     * @param SwatchInterfaceFactory $swatchFactory
     * @param SwatchCollectionFactory $swatchCollectionFactory
     * @param SwatchSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        SwatchResource $resource,
        SwatchInterfaceFactory $swatchFactory,
        SwatchCollectionFactory $swatchCollectionFactory,
        SwatchSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->swatchFactory = $swatchFactory;
        $this->swatchCollectionFactory = $swatchCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(SwatchInterface $swatch)
    {
        try {
            $this->resource->save($swatch);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the swatch: %1',
                $exception->getMessage()
            ));
        }
        return $swatch;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($swatchId)
    {
        $swatch = $this->swatchFactory->create();
        $this->resource->load($swatch, $swatchId);
        if (!$swatch->getId()) {
            throw new NoSuchEntityException(__('swatch with id "%1" does not exist.', $swatchId));
        }
        return $swatch;
    }

    /**
     * {@inheritdoc}
     */
    public function getListByPostId($postId)
    {
        $swatchCollection = $this->swatchCollectionFactory->create();
        $swatchCollection->addFieldToFilter('item_id', $postId);
        $swatchCollection->addOrder('position', 'ASC');
        return $swatchCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(SwatchInterface $swatch)
    {
        try {
            $this->resource->delete($swatch);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Swatch: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($swatchId)
    {
        return $this->delete($this->getById($swatchId));
    }
}
