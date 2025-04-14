<?php


namespace BelVG\LayoutCustomizer\Setup;


class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    private $eavSetupFactory;

    public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function createLayoutProductAttribute($eavSetup)
    {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            \BelVG\LayoutCustomizer\Helper\Data::PRODUCT_LAYOUT_ATTR, //'belvg_layout',
            [
                'attribute_set' => 'Default',
                'group' => 'Product Details',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Layout',
                'input' => 'select',
                'class' => '',
                'source' => 'BelVG\LayoutCustomizer\Model\Config\Source\Layout\Options',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => ''
            ]
        );
    }

    public function upgrade(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (!$context->getVersion()) {
            //no previous version found, installation, InstallSchema was just executed
            //be careful, since everything below is true for installation !

            $this->createLayoutProductAttribute($eavSetup);
        }

        if (version_compare($context->getVersion(), '1.0.7') < 0) {
            //code to upgrade to 1.0.5

            $this->createLayoutProductAttribute($eavSetup);
        }

        /*if (version_compare($context->getVersion(), '1.0.6') < 0) {
           //code to upgrade to 1.0.4

           $eavSetup->removeAttribute(
               \Magento\Catalog\Model\Product::ENTITY,
               \BelVG\LayoutCustomizer\Helper\Data::PRODUCT_LAYOUT_ATTR
           );
       } */

        $setup->endSetup();
    }

}
