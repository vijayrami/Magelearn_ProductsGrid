<?php

namespace Magelearn\ProductsGrid\Controller\Adminhtml;

use Magelearn\ProductsGrid\Model\CategoryFactory;
use Magelearn\ProductsGrid\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magelearn\ProductsGrid\Model\TagFactory;
use Magelearn\ProductsGrid\Model\ResourceModel\Tag\CollectionFactory as TagCollectionFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\View\Model\Layout\Update\ValidatorFactory;

class PostDataProcessor
{
    /**
     * Date Filter
     *
     * @var Date
     */
    protected $dateFilter;

    /**
     * Validation Factory
     *
     * @var ValidatorFactory
     */
    protected $validatorFactory;

    /**
     * Message Manager
     *
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * CategoryCollectionFactory
     *
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;
    
    /**
     * TagCollectionFactory
     *
     * @var TagCollectionFactory
     */
    protected $tagCollectionFactory;

    /**
     * PostDataProcessor Class constructor
     *
     * @param Date               $dateFilter         DateFiletr
     * @param ManagerInterface   $messageManager     MessageManager
     * @param ValidatorFactory   $validatorFactory   ValidationFactory
     * @param CategoryCollectionFactory  $categoryCollectionFactory
     * @param TagCollectionFactory  $tagCollectionFactory
     */
    public function __construct(
        Date $dateFilter,
        ManagerInterface $messageManager,
        ValidatorFactory $validatorFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        TagCollectionFactory  $tagCollectionFactory
    ) {
        $this->dateFilter = $dateFilter;
        $this->messageManager = $messageManager;
        $this->validatorFactory = $validatorFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->tagCollectionFactory = $tagCollectionFactory;
    }

    /**
     * Validate post data
     *
     * @param array $data Datapost
     *
     * @return bool
     */
    public function validate($data, $validate)
    {
        $errorNo1 = $this->validateRequireEntry($data);
        $errorNo2 = $this->checkNameExist($data, $validate);
        $errorNo3 = true;

        if (!in_array($data['enabled'], [0,1]) || $data['enabled'] == '' || $data['enabled'] === null) {
            $errorNo3 = false;
            $this->messageManager->addErrorMessage(
                __("Please enter valid status.")
            );
        }

        return $errorNo1 && $errorNo2 && $errorNo3;
    }    

    /**
     * Check if required fields is not empty
     *
     * @param array $data RequireFields
     *
     * @return bool
     */
    public function validateRequireEntry(array $data)
    {
        $requiredFields = [
            'name' => __('Name')
        ];

        $errorNo = true;
        foreach ($data as $field => $value) {
            if (in_array($field, array_keys($requiredFields)) && $value == '') {
                $errorNo = false;
                $this->messageManager->addErrorMessage(
                    __(
                        'To apply changes you should fill valid value to required "%1" field',
                        $requiredFields[$field]
                    )
                );
            }
        }
        return $errorNo;
    }

    /**
     * Check if name is already exist or not
     *
     * @param array $data RequireFields
     *
     * @return bool
     */
    public function checkNameExist(array $data, String $validate)
    {
        $errorNo = true;
        
        if($validate == 'category') {
            if (isset($data['category_id'])) {
                $prapareCollection = $this->categoryCollectionFactory->create()
                ->addFieldToFilter('category_id', ['neq' => $data['category_id']]);
            } else {
                $prapareCollection = $this->categoryCollectionFactory->create();
            }
        }
        
        if($validate == 'tag') {
            if (isset($data['tag_id'])) {
                $prapareCollection = $this->tagCollectionFactory->create()
                ->addFieldToFilter('tag_id', ['neq' => $data['tag_id']]);
            } else {
                $prapareCollection = $this->tagCollectionFactory->create();
            }
        }
        
        foreach ($prapareCollection as $collection) {
            $collectionName = trim(mb_strtolower(preg_replace('/\s+/', ' ', $collection->getName()), 'UTF-8'));
            if (trim(preg_replace('/\s+/', ' ', mb_strtolower($data['name'], 'UTF-8'))) == $collectionName) {
                $errorNo = false;
                $this->messageManager->addErrorMessage(
                    __('This name is already exist.')
                );
            }
        }
        return $errorNo;
    }
}