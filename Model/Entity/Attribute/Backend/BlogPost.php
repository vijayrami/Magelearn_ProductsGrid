<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magelearn\ProductsGrid\Model\Entity\Attribute\Backend;

use Magelearn\ProductsGrid\Model\ResourceModel\Post\CollectionFactory;

class BlogPost extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
	/**
	 * @var CollectionFactory
	 */
    public $postCollectionFactory;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \Magento\Store\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        CollectionFactory $postCollectionFactory
    ) {
        $this->_postCollectionFactory = $postCollectionFactory;
    }
	
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
			$result[] = ['value'=>'', 'label'=>__(' ')];
			
			$posts = $this->_postCollectionFactory->create();
			$posts->addFieldToFilter('enabled', '1');
			if(count($posts)>0){
			    foreach($posts as $post){
			        $result[] = ['value'=>$post->getId(), 'label'=>$post->getName()];
				}
			}
			
			$this->_options = $result;

        }
        return $this->_options;
    }

}
