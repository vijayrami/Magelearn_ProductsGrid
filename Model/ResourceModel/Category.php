<?php

namespace Magelearn\ProductsGrid\Model\ResourceModel;

use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magelearn\ProductsGrid\Helper\Data;
use Magelearn\ProductsGrid\Model\Category as CategoryModel;
use Zend_Db_Expr;

/**
 * Class Category
 * @package Magelearn\ProductsGrid\Model\ResourceModel
 */
class Category extends AbstractDb
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
     * Post relation model
     *
     * @var string
     */
    public $categoryPostTable;

    /**
     * @var Data
     */
    public $helperData;

    /**
     * Category constructor.
     *
     * @param Data $helperData
     * @param DateTime $date
     * @param ManagerInterface $eventManager
     * @param Context $context
     */
    public function __construct(
        Context $context,
        DateTime $date,
        ManagerInterface $eventManager,
        Data $helperData
    ) {
        $this->helperData   = $helperData;
        $this->date         = $date;
        $this->eventManager = $eventManager;

        parent::__construct($context);

        $this->categoryPostTable = $this->getTable('magelearn_item_post_category');
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magelearn_item_category', 'category_id');
    }

    /**
     * Retrieves Blog Category Name from DB by passed id.
     *
     * @param int $id
     *
     * @return string
     * @throws LocalizedException
     */
    public function getCategoryNameById($id)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from($this->getMainTable(), 'name')
            ->where('category_id = :category_id');
        $binds   = ['category_id' => (int) $id];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Before save call back
     *
     * @param AbstractModel $object
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $object->setUpdatedAt($this->date->date());
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->date->date());
        }
        /** @var CategoryModel $object */
        parent::_beforeSave($object);

        if (is_array($object->getStoreIds())) {
            $object->setStoreIds(implode(',', $object->getStoreIds()));
        }

        return $this;
    }

    /**
     * @param AbstractModel $object
     *
     * @return AbstractDb
     * @throws LocalizedException
     */
    protected function _afterSave(AbstractModel $object)
    {
        /** @var CategoryModel $object */
        $this->savePostRelation($object);

        return parent::_afterSave($object);
    }

    /**
     * @param CategoryModel $category
     *
     * @return array
     */
    public function getPostsPosition(CategoryModel $category)
    {
        $select = $this->getConnection()->select()->from(
            $this->categoryPostTable,
            ['item_id', 'position']
        )
            ->where(
                'category_id = :category_id'
            );
        $bind   = ['category_id' => (int) $category->getId()];

        return $this->getConnection()->fetchPairs($select, $bind);
    }

    /**
     * @param CategoryModel $category
     *
     * @return $this
     */
    public function savePostRelation(CategoryModel $category)
    {
        $category->setIsChangedPostList(false);
        $id    = $category->getId();
        $posts = $category->getPostsData();
        if ($posts === null) {
            return $this;
        }
        $oldPosts = $category->getPostsPosition();
        $insert   = array_diff_key($posts, $oldPosts);
        $delete   = array_diff_key($oldPosts, $posts);
        $update   = array_intersect_key($posts, $oldPosts);
        $_update  = [];
        foreach ($update as $key => $position) {
            if (isset($oldPosts[$key]) && $oldPosts[$key] != $position) {
                $_update[$key] = $position;
            }
        }
        $update  = $_update;
        $adapter = $this->getConnection();
        if (!empty($delete)) {
            $condition = ['item_id IN(?)' => array_keys($delete), 'category_id=?' => $id];
            $adapter->delete($this->categoryPostTable, $condition);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $postId => $position) {
                $data[] = [
                    'category_id' => (int) $id,
                    'item_id'     => (int) $postId,
                    'position'    => (int) $position
                ];
            }
            $adapter->insertMultiple($this->categoryPostTable, $data);
        }
        if (!empty($update)) {
            foreach ($update as $postId => $position) {
                $where = ['category_id = ?' => (int) $id, 'item_id = ?' => (int) $postId];
                $bind  = ['position' => (int) $position];
                $adapter->update($this->categoryPostTable, $bind, $where);
            }
        }
        if (!empty($insert) || !empty($delete)) {
            $postIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'magelearn_item_category_change_posts',
                ['category' => $category, 'item_ids' => $postIds]
            );
        }
        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $category->setIsChangedPostList(true);
            $postIds = array_keys($insert + $delete + $update);
            $category->setAffectedPostIds($postIds);
        }

        return $this;
    }
}
