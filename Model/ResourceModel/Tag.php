<?php

namespace Magelearn\ProductsGrid\Model\ResourceModel;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magelearn\ProductsGrid\Helper\Data;

/**
 * Class Tag
 * @package Magelearn\ProductsGrid\Model\ResourceModel
 */
class Tag extends AbstractDb
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
    public $tagPostTable;

    /**
     * @var Data
     */
    public $helperData;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * Tag constructor.
     *
     * @param Context $context
     * @param ManagerInterface $eventManager
     * @param DateTime $date
     * @param RequestInterface $request
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        ManagerInterface $eventManager,
        DateTime $date,
        RequestInterface $request,
        Data $helperData
    ) {
        $this->helperData = $helperData;
        $this->date = $date;
        $this->eventManager = $eventManager;
        $this->request = $request;

        parent::__construct($context);

        $this->tagPostTable = $this->getTable('magelearn_item_post_tag');
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magelearn_item_tag', 'tag_id');
    }

    /**
     * Retrieves Tag Name from DB by passed id.
     *
     * @param $id
     *
     * @return string
     * @throws LocalizedException
     */
    public function getTagNameById($id)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'name')
            ->where('tag_id = :tag_id');
        $binds = ['tag_id' => (int)$id];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * @inheritdoc
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $object->setUpdatedAt($this->date->date());
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->date->date());
        }

        if (is_array($object->getStoreIds())) {
            $object->setStoreIds(implode(',', $object->getStoreIds()));
        }

        return parent::_beforeSave($object);
    }

    /**
     * @inheritdoc
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->savePostRelation($object);

        return parent::_afterSave($object);
    }

    /**
     * @param \Magelearn\ProductsGrid\Model\Tag $tag
     *
     * @return array
     */
    public function getPostsPosition(\Magelearn\ProductsGrid\Model\Tag $tag)
    {
        $select = $this->getConnection()->select()
            ->from($this->tagPostTable, ['item_id', 'position'])
            ->where('tag_id = :tag_id');

        $bind = ['tag_id' => (int)$tag->getId()];

        return $this->getConnection()->fetchPairs($select, $bind);
    }

    /**
     * @param \Magelearn\ProductsGrid\Model\Tag $tag
     *
     * @return $this
     */
    protected function savePostRelation(\Magelearn\ProductsGrid\Model\Tag $tag)
    {
        $tag->setIsChangedPostList(false);
        $id = $tag->getId();
        $posts = $tag->getPostsData();
        $oldPosts = $tag->getPostsPosition();
        if (is_array($posts)) {
            $insert = array_diff_key($posts, $oldPosts);
            $delete = array_diff_key($oldPosts, $posts);
            $update = array_intersect_key($posts, $oldPosts);
            $_update = [];
            foreach ($update as $key => $settings) {
                if (isset($oldPosts[$key]) && $oldPosts[$key] != $settings['position']) {
                    $_update[$key] = $settings;
                }
            }
            $update = $_update;
        }
        $adapter = $this->getConnection();
        if ($posts === null && $this->request->getActionName() === 'save') {
            foreach (array_keys($oldPosts) as $value) {
                $condition = ['item_id =?' => (int)$value, 'tag_id=?' => (int)$id];
                $adapter->delete($this->tagPostTable, $condition);
            }

            return $this;
        }
        if (!empty($delete)) {
            foreach (array_keys($delete) as $value) {
                $condition = ['item_id =?' => (int)$value, 'tag_id=?' => (int)$id];
                $adapter->delete($this->tagPostTable, $condition);
            }
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $postId => $position) {
                $data[] = [
                    'tag_id' => (int)$id,
                    'item_id' => (int)$postId,
                    'position' => (int)$position['position']
                ];
            }
            $adapter->insertMultiple($this->tagPostTable, $data);
        }
        if (!empty($update)) {
            foreach ($update as $postId => $position) {
                $where = ['tag_id = ?' => (int)$id, 'item_id = ?' => (int)$postId];
                $bind = ['position' => (int)$position['position']];
                $adapter->update($this->tagPostTable, $bind, $where);
            }
        }
        if (!empty($insert) || !empty($delete)) {
            $postIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'magelearn_item_tag_change_posts',
                ['tag' => $tag, 'item_ids' => $postIds]
            );
        }
        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $tag->setIsChangedPostList(true);
            $postIds = array_keys($insert + $delete + $update);
            $tag->setAffectedPostIds($postIds);
        }

        return $this;
    }
}
