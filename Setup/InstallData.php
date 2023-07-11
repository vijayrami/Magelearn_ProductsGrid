<?php

namespace Magelearn\ProductsGrid\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class InstallData implements InstallDataInterface
{
	private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $productTypes = [
            \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
            \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE,
            \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
            \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE,
            \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
        ];
        $productTypes = join(',', $productTypes);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'magelearn_post',
            [
				'group' => 'Content',
                'sort_order' => 150,
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Blog Post',
                'input' => 'select',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
				'source' => 'Magelearn\ProductsGrid\Model\Entity\Attribute\Backend\BlogPost',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_in_advanced_search' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => $productTypes,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
            ]
        );
    }
}

