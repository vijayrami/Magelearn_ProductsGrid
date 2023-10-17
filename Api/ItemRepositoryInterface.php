<?php

namespace Magelearn\ProductsGrid\Api;

use Exception;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class ItemRepositoryInterface
 * @package Magelearn\ProductsGrid\Api
 */
interface ItemRepositoryInterface
{
    /**
     * @return \Magelearn\ProductsGrid\Api\Data\PostInterface[]
     */
    public function getAllPost();

    /**
     * @param string $tagName
     *
     * @return \Magelearn\ProductsGrid\Api\Data\PostInterface[]
     */
    public function getPostByTagName($tagName);

    /**
     * @param string $postId
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getProductByPost($postId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magelearn\ProductsGrid\Api\Data\SearchResult\PostSearchResultInterface
     * @throws NoSuchEntityException
     */
    public function getPostList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Create Post
     *
     * @param \Magelearn\ProductsGrid\Api\Data\PostInterface $post
     *
     * @return \Magelearn\ProductsGrid\Api\Data\PostInterface
     * @throws Exception
     */
    public function createPost($post);

    /**
     * Delete Post
     *
     * @param string $postId
     *
     * @return string
     */
    public function deletePost($postId);

    /**
     * @param string $postId
     * @param \Magelearn\ProductsGrid\Api\Data\PostInterface $post
     *
     * @return \Magelearn\ProductsGrid\Api\Data\PostInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function updatePost($postId, $post);

    /**
     * Get All Tag
     *
     * @return \Magelearn\ProductsGrid\Api\Data\TagInterface[]
     */
    public function getAllTag();

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magelearn\ProductsGrid\Api\Data\SearchResult\TagSearchResultInterface
     */
    public function getTagList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Create Post
     *
     * @param \Magelearn\ProductsGrid\Api\Data\TagInterface $tag
     *
     * @return \Magelearn\ProductsGrid\Api\Data\TagInterface
     * @throws Exception
     */
    public function createTag($tag);

    /**
     * Delete Tag
     *
     * @param string $tagId
     *
     * @return string
     */
    public function deleteTag($tagId);

    /**
     * @param string $tagId
     * @param \Magelearn\ProductsGrid\Api\Data\TagInterface $tag
     *
     * @return \Magelearn\ProductsGrid\Api\Data\TagInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function updateTag($tagId, $tag);

    /**
     * Get All Category
     *
     * @return \Magelearn\ProductsGrid\Api\Data\CategoryInterface[]
     */
    public function getAllCategory();

    /**
     * Get Category List
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magelearn\ProductsGrid\Api\Data\SearchResult\CategorySearchResultInterface
     */
    public function getCategoryList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param string $categoryId
     *
     * @return \Magelearn\ProductsGrid\Api\Data\PostInterface[]
     */
    public function getPostsByCategoryId($categoryId);

    /**
     * @param string $postId
     *
     * @return \Magelearn\ProductsGrid\Api\Data\CategoryInterface[]
     */
    public function getCategoriesByPostId($postId);

    /**
     * Create Category
     *
     * @param \Magelearn\ProductsGrid\Api\Data\CategoryInterface $category
     *
     * @return \Magelearn\ProductsGrid\Api\Data\CategoryInterface
     * @throws Exception
     */
    public function createCategory($category);

    /**
     * Delete Category
     *
     * @param string $categoryId
     *
     * @return string
     */
    public function deleteCategory($categoryId);

    /**
     * @param string $categoryId
     * @param \Magelearn\ProductsGrid\Api\Data\CategoryInterface $category
     *
     * @return \Magelearn\ProductsGrid\Api\Data\CategoryInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function updateCategory($categoryId, $category);
}
