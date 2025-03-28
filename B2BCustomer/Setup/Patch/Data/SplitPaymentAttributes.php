<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Config;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class SplitPaymentAttributes implements DataPatchInterface
{

    const ATTRIBUTES_DATA = [
        'b2b_split_payment_1' => 'B2B first payment amount',
        'b2b_split_payment_2' => 'B2B second payment amount',
        'b2b_split_payment_3' => 'B2B third payment amount',
        'b2b_split_period_1' => 'B2B first period (days)',
        'b2b_split_period_2' => 'B2B second period (days)',
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
                'backend' => 'BelVG\B2BCustomer\Model\Attribute\Backend\SplitPayment',
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
        $customerSetup->addAttribute(Customer::ENTITY, 'b2b_split_payment_data', [
            'type' => 'varchar',
            'input' => 'text',
            'label' => 'B2B Split payment data',
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
        $newAttribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'b2b_split_payment_data');
        $newAttribute->addData([
            'used_in_forms' => ['adminhtml_checkout','adminhtml_customer','customer_account_edit','customer_account_create'],
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroup
        ]);
        /** @phpstan-ignore-next-line */
        $newAttribute->save();

        return $this;
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }
}
