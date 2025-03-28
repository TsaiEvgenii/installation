<?php

namespace BelVG\B2BCustomer\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetup;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $_eavAttribute;

    protected $eavSetupFactory;


    public function __construct(
        EavSetup                                          $eavSetupFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->_eavAttribute = $eavAttribute;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup->startSetup();
            $entityType = $this->eavSetupFactory->getEntityType(\Magento\Customer\Model\Customer::ENTITY);
            $entityTypeId = $entityType['entity_type_id'];
            $attributeId = $this->_eavAttribute->getIdByCode(\Magento\Customer\Model\Customer::ENTITY, 'b2b_date');
            $this->eavSetupFactory->updateAttribute($entityTypeId, $attributeId, 'backend_type', 'datetime');
            $this->eavSetupFactory->updateAttribute($entityTypeId, $attributeId, 'frontend_input', 'date');
            $this->eavSetupFactory->updateAttribute($entityTypeId, $attributeId, 'backend_model', \Magento\Eav\Model\Entity\Attribute\Backend\Datetime::class);
            $attributeId = $this->_eavAttribute->getIdByCode(\Magento\Customer\Model\Customer::ENTITY, 'b2b_discount');
            $this->eavSetupFactory->updateAttribute($entityTypeId, $attributeId, 'backend_type', 'int');
            $this->eavSetupFactory->updateAttribute($entityTypeId, $attributeId, 'backend_model', \BelVG\B2BCustomer\Model\Entity\Attribute\Backend\DiscountField::class);
            $setup->endSetup();
        }
    }
}
