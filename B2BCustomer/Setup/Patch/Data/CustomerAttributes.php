<?php
/*
 *  @package Vinduesgrossisten
 *   * @author  Tsai<tsai.evgenii@belvg.com>
 *   * @Copyright
 */

namespace BelVG\B2BCustomer\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Config;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class CustomerAttributes implements DataPatchInterface
{

    const ATTRIBUTES_DATA = [
        'b2b_cvr' => 'B2B CVR number',
        'b2b_date' => 'B2B date for credit evaluation',
        'b2b_discount' => 'B2B Discount (%)'
    ];


    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * AccountPurposeCustomerAttribute constructor.
     * @param ModuleDataSetupInterface $setup
     * @param Config $eavConfig
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        Config $eavConfig,
        CustomerSetupFactory $customerSetupFactory
    )
    {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->setup = $setup;
        $this->eavConfig = $eavConfig;
    }

    /** We'll add our customer attribute here */
    public function apply()
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->setup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerSetup->getDefaultAttributeSetId($customerEntity->getEntityTypeId());
        $attributeGroup = $customerSetup->getDefaultAttributeGroupId($customerEntity->getEntityTypeId(), $attributeSetId);
        foreach (self::ATTRIBUTES_DATA as $attributeKey => $attributeTitle) {
            $customerSetup->addAttribute(Customer::ENTITY, $attributeKey, [
                'type' => 'varchar',
                'input' => 'text',
                'label' => $attributeTitle,
                'required' => false,
                'default' => '',
                'visible' => true,
                'user_defined' => true,
                'system' => false,
                'is_visible_in_grid' => false,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'position' => 300
            ]);
            $newAttribute = $this->eavConfig->getAttribute(Customer::ENTITY, $attributeKey);
            $newAttribute->addData([
                'used_in_forms' => ['adminhtml_checkout','adminhtml_customer','customer_account_edit','customer_account_create'],
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroup
            ]);
            /** @phpstan-ignore-next-line */
            $newAttribute->save();
        }
        return $this;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
