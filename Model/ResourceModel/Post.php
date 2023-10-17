<?php

namespace Magelearn\ProductsGrid\Model\ResourceModel;

use Magento\Backend\Model\Auth;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magelearn\ProductsGrid\Helper\Data;
use Magelearn\ProductsGrid\Model\Post as PostModel;

/**
 * Class Post
 * @package Magelearn\ProductsGrid\Model\ResourceModel
 */
class Post extends AbstractDb
{
    /**
     * Date model
     *
     * @var DateTime
     */
    public $date;

    /**
     * Event Manager
     *
     * @var ManagerInterface
     */
    public $eventManager;

    /**
     * Tag relation model
     *
     * @var string
     */
    public $postTagTable;

    /**
     * Item Category relation model
     *
     * @var string
     */
    public $postCategoryTable;

    /**
     * @var string
     */
    public $postProductTable;

    /**
     * @var Data
     */
    public $helperData;

    /**
     * @var Auth
     */
    protected $_auth;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * Post constructor.
     *
     * @param Context $context
     * @param DateTime $date
     * @param ManagerInterface $eventManager
     * @param Auth $auth
     * @param Data $helperData
     * @param RequestInterface $request
     */
    public function __construct(
        Context $context,
        DateTime $date,
        ManagerInterface $eventManager,
        Auth $auth,
        Data $helperData,
        RequestInterface $request
    ) {
        $this->date           = $date;
        $this->eventManager   = $eventManager;
        $this->_auth          = $auth;
        $this->helperData     = $helperData;
        $this->_request       = $request;

        parent::__construct($context);

        $this->postTagTable      = $this->getTable('magelearn_item_post_tag');
        $this->postCategoryTable = $this->getTable('magelearn_item_post_category');
        $this->postProductTable  = $this->getTable('magelearn_item_post_product');
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magelearn_item_post', 'item_id');
    }

    /**
     * Retrieves Post Name from DB by passed id.
     *
     * @param int $id
     *
     * @return string
     * @throws LocalizedException
     */
    public function getPostNameById($id)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from($this->getMainTable(), 'name')
            ->where('item_id = :item_id');
        $binds   = ['item_id' => (int) $id];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * before save callback
     *
     * @param AbstractModel $object
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if (is_array($object->getStoreIds())) {
            $object->setStoreIds(implode(',', $object->getStoreIds()));
        }

        return $this;
    }

    /**
     * @param PostModel|AbstractModel $object
     * @return AbstractDb
     * @throws LocalizedException
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->saveTagRelation($object);
        $this->saveCategoryRelation($object);
        $this->saveProductRelation($object);

        return parent::_afterSave($object);
    }

    /**
     * @param PostModel $post
     *
     * @return $this
     * @throws LocalizedException
     */
    public function saveTagRelation(PostModel $post)
    {
        $post->setIsChangedTagList(false);
        $id      = $post->getId();
        $tags    = $post->getTagsIds();
        $oldTags = $post->getTagIds();

        if ($tags === null) {
            return $this;
        }

        $insert  = array_diff($tags, $oldTags);
        $delete  = array_diff($oldTags, $tags);
        $adapter = $this->getConnection();
        if (!empty($delete)) {
            $condition = ['tag_id IN(?)' => $delete, 'item_id=?' => $id];
            $adapter->delete($this->postTagTable, $condition);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $tagId) {
                $data[] = [
                    'item_id'  => (int) $id,
                    'tag_id'   => (int) $tagId,
                    'position' => 1
                ];
            }
            $adapter->insertMultiple($this->postTagTable, $data);
        }
        if (!empty($insert) || !empty($delete)) {
            $tagIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'magelearn_item_post_change_tags',
                ['post' => $post, 'tag_ids' => $tagIds]
            );
        }

        if (!empty($insert) || !empty($delete)) {
            $post->setIsChangedTagList(true);
            $tagIds = array_keys($insert + $delete);
            $post->setAffectedTagIds($tagIds);
        }

        return $this;
    }

    /**
     * @param PostModel $post
     *
     * @return $this
     * @throws LocalizedException
     */
    public function saveCategoryRelation(PostModel $post)
    {
        $post->setIsChangedCategoryList(false);
        $id             = $post->getId();
        $categories     = $post->getCategoriesIds();
        $oldCategoryIds = $post->getCategoryIds();

        if ($categories === null) {
            return $this;
        }

        $insert         = array_diff($categories, $oldCategoryIds);
        $delete         = array_diff($oldCategoryIds, $categories);
        $adapter        = $this->getConnection();

        if (!empty($delete)) {
            $condition = ['category_id IN(?)' => $delete, 'item_id=?' => $id];
            $adapter->delete($this->postCategoryTable, $condition);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $categoryId) {
                $data[] = [
                    'item_id'     => (int) $id,
                    'category_id' => (int) $categoryId,
                    'position'    => 1
                ];
            }
            $adapter->insertMultiple($this->postCategoryTable, $data);
        }
        if (!empty($insert) || !empty($delete)) {
            $categoryIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'magelearn_item_post_change_categories',
                ['post' => $post, 'category_ids' => $categoryIds]
            );
        }
        if (!empty($insert) || !empty($delete)) {
            $post->setIsChangedCategoryList(true);
            $categoryIds = array_keys($insert + $delete);
            $post->setAffectedCategoryIds($categoryIds);
        }

        return $this;
    }

    /**
     * @param PostModel $post
     *
     * @return array
     */
    public function getCategoryIds(PostModel $post)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()->from(
            $this->postCategoryTable,
            'category_id'
        )
            ->where(
                'item_id = ?',
                (int) $post->getId()
            );

        return $adapter->fetchCol($select);
    }

    /**
     * @param PostModel $post
     *
     * @return array
     */
    public function getTagIds(PostModel $post)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()->from(
            $this->postTagTable,
            'tag_id'
        )
            ->where(
                'item_id = ?',
                (int) $post->getId()
            );

        return $adapter->fetchCol($select);
    }

    /**
     * @param PostModel $post
     *
     * @return $this
     */
    public function saveProductRelation(PostModel $post)
    {
        $post->setIsChangedProductList(false);
        $id          = $post->getId();
        $products    = $post->getProductsData();
        $oldProducts = $post->getProductsPosition();

        if (is_array($products)) {
            $insert  = array_diff_key($products, $oldProducts);
            $delete  = array_diff_key($oldProducts, $products);
            $update  = array_intersect_key($products, $oldProducts);
            $_update = [];
            foreach ($update as $key => $settings) {
                if (isset($oldProducts[$key]) && $oldProducts[$key] != $settings['position']) {
                    $_update[$key] = $settings;
                }
            }
            $update = $_update;
        }
        $adapter = $this->getConnection();
        if ($products === null && $this->_request->getActionName() === 'save') {
            foreach (array_keys($oldProducts) as $value) {
                $condition = ['entity_id =?' => (int) $value, 'item_id=?' => (int) $id];
                $adapter->delete($this->postProductTable, $condition);
            }

            return $this;
        }
        if (!empty($delete)) {
            foreach (array_keys($delete) as $value) {
                $condition = ['entity_id =?' => (int) $value, 'item_id=?' => (int) $id];
                $adapter->delete($this->postProductTable, $condition);
            }
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $entityId => $position) {
                $data[] = [
                    'item_id'   => (int) $id,
                    'entity_id' => (int) $entityId,
                    'position'  => (int) $position['position']
                ];
            }
            $adapter->insertMultiple($this->postProductTable, $data);
        }
        if (!empty($update)) {
            foreach ($update as $entityId => $position) {
                $where = ['item_id = ?' => (int) $id, 'entity_id = ?' => (int) $entityId];
                $bind  = ['position' => (int) $position['position']];
                $adapter->update($this->postProductTable, $bind, $where);
            }
        }
        if (!empty($insert) || !empty($delete)) {
            $entityIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'magelearn_item_post_change_products',
                ['post' => $post, 'entity_ids' => $entityIds]
            );
        }
        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $post->setIsChangedProductList(true);
            $entityIds = array_keys($insert + $delete + $update);
            $post->setAffectedEntityIds($entityIds);
        }

        return $this;
    }

    /**
     * @param PostModel $post
     *
     * @return array
     */
    public function getProductsPosition(PostModel $post)
    {
        $select = $this->getConnection()->select()->from(
            $this->postProductTable,
            ['entity_id', 'position']
        )
            ->where(
                'item_id = :item_id'
            );
        $bind   = ['item_id' => (int) $post->getId()];

        return $this->getConnection()->fetchPairs($select, $bind);
    }
    
}
