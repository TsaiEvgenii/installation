<?php

namespace BelVG\LayoutCustomizer\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    /**
     * @var SchemaSetupInterface
     */
    protected $setup;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * Alter table and add columns
     *
     * @param $columns
     * @param $table_name
     */
    private function addColumns($columns, $table_name)
    {
        foreach ($columns as $column_name => $column_definition) {
            if ($this->connection->tableColumnExists($table_name, $column_name) === false) {
                $this->setup->getConnection()->addColumn(
                    $table_name,
                    $column_name,
                    $column_definition
                );
            }
        }
        unset($column);
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $this->setup = $setup;
        $this->connection = $setup->getConnection();

        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.8', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('catalog_product_option_type_value'),
                'mageworx_optiontemplates_group_option_type_id',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => 40,
                    'after' => 'mageworx_option_type_id',
                    'comment' => 'reference with DB-table `mageworx_optiontemplates_group_option_type_value` (Belvg_LayoutCustomizer)',
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.9', '<')) {
            if($setup->getConnection()->isTableExists('belvg_layoutcustomizer_layoutrestriction')) {

                $setup->getConnection()->dropColumn(
                    $setup->getTable('belvg_layoutcustomizer_layoutrestriction'),
                    'layout_id'
                );
            }
        }

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            if($setup->getConnection()->isTableExists('belvg_layoutcustomizer_layoutrestriction')){
                $setup->getConnection()->dropColumn(
                    $setup->getTable('belvg_layoutcustomizer_layoutrestriction'),
                    'key'
                );
                $setup->getConnection()->dropColumn(
                    $setup->getTable('belvg_layoutcustomizer_layoutrestriction'),
                    'value'
                );
            }
        }

        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            $setup->getConnection()->dropColumn(
                $setup->getTable('belvg_layoutcustomizer_layout'),
                'sqm_price_step2'
            );
        }

        if (version_compare($context->getVersion(), '1.1.4', '<')) {
            if($setup->getConnection()->isTableExists('belvg_layoutcustomizer_layoutblock')) {
                $setup->getConnection()->dropIndex('belvg_layoutcustomizer_layoutblock', 'BELVG_LAYOUTCUSTOMIZER_LAYOUTBLOCK_IDENTIFIER');
            }
        }

        if (version_compare($context->getVersion(), '1.1.5', '<')) {
            $setup->getConnection()->dropColumn(
                $setup->getTable('belvg_layoutcustomizer_layout'),
                'horizontal_frame'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('belvg_layoutcustomizer_layout'),
                'vertical_frame'
            );
        }

        if (version_compare($context->getVersion(), '1.1.9', '<')) {
            $key_name_sql = $setup->getConnection()->select()
                ->from('INFORMATION_SCHEMA.KEY_COLUMN_USAGE', ['CONSTRAINT_NAME'])
                ->where('TABLE_NAME = ?', $setup->getTable('belvg_layoutcustomizer_layoutblock'))
                ->where('COLUMN_NAME = ?', 'layoutblockopentype_id');
            $key_name = $setup->getConnection()->fetchOne($key_name_sql);
            if($setup->getConnection()->isTableExists('belvg_layoutcustomizer_layoutblock')) {
                $setup->getConnection()->dropForeignKey('belvg_layoutcustomizer_layoutblock', $key_name);
            }

            $tableBlock = $installer->getTable('belvg_layoutcustomizer_layoutblock');
            if($installer->getConnection()->isTableExists('belvg_layoutcustomizer_layoutblockopentype')){
                $tableBlockOpenType = $installer->getTable('belvg_layoutcustomizer_layoutblockopentype');
                $setup->getConnection()->addForeignKey(
                    $installer->getFkName($tableBlock, 'layoutblockopentype_id', $tableBlockOpenType, 'layoutblockopentype_id'),
                    $tableBlock,
                    'layoutblockopentype_id',
                    $tableBlockOpenType,
                    'layoutblockopentype_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
                );
            }
        }

        if (version_compare($context->getVersion(), '1.1.10', '<')) {
            $setup->getConnection()->dropColumn(
                $setup->getTable('belvg_layoutcustomizer_layout'),
                'sqm_level_step2'
            );
        }

        if (version_compare($context->getVersion(), '1.1.11', '<')) {
            // Add constraints for tables referencing MageWorx_OptionTemplates options
            $connection = $setup->getConnection();

            // External options
            $tables = [
                'belvg_layoutcustomizer_layout_block_parameter_option',
                'belvg_layoutcustomizer_layout_feature_parameter_option'
            ];
            $optionTypeTable = 'mageworx_optiontemplates_group_option_type_value';
            foreach ($tables as $table) {
                $connection->addForeignKey(
                    $connection->getForeignKeyName(
                        $table, 'option_type_id',
                        $optionTypeTable, 'option_type_id'),
                    $table, 'option_type_id',
                    $optionTypeTable, 'option_type_id',
                    AdapterInterface::FK_ACTION_CASCADE,
                    true /* purge orphans*/);
            }

            // External parameters
            $table = 'belvg_layoutcustomizer_layout_measurement';
            $paramTable = 'mageworx_optiontemplates_group_option';
            $connection->addForeignKey(
                $connection->getForeignKeyName(
                    $table, 'param_id',
                    $paramTable, 'option_id'),
                $table, 'param_id',
                $paramTable, 'option_id',
                AdapterInterface::FK_ACTION_SET_NULL,
                true /* purge orphans */);
        }

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $table_name = $this->setup->getTable('belvg_layoutcustomizer_layoutstore');
            $columns = [
                'inoutcolor_price_both_diff' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'    => '12,4',
                    'unsigned'  => false,
                    'nullable'  => true,
                    'comment'   => 'Both different; inside/outside color; influence to price calc  (added by BelVG_InsideOutsideColorPrice)',
                ],
                'inoutcolor_price_both_same' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'    => '12,4',
                    'unsigned'  => false,
                    'nullable'  => true,
                    'comment'   => 'Both same; inside/outside color; influence to price calc  (added by BelVG_InsideOutsideColorPrice)',
                ],
                'inoutcolor_price_inside_otherwhite' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'    => '12,4',
                    'unsigned'  => false,
                    'nullable'  => true,
                    'comment'   => 'Inside otherwhite; inside/outside color; influence to price calc  (added by BelVG_InsideOutsideColorPrice)',
                ],
                'inoutcolor_price_outside_otherwhite' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'    => '12,4',
                    'unsigned'  => false,
                    'nullable'  => true,
                    'comment'   => 'Outside otherwhite; inside/outside color; influence to price calc  (added by BelVG_InsideOutsideColorPrice)',
                ],
            ];

            $this->addColumns($columns, $table_name);
        }

        // Old table cleanup
        if (version_compare($context->getVersion(), '1.2.3', '<')) {
            $oldTables = [
                'belvg_layoutcustomizer_layoutrestriction',
                'belvg_layoutcustomizer_layoutrestrictionoption',
                'belvg_layoutcustomizer_layoutblock_shapeparam',
                'belvg_layoutcustomizer_layoutblock',
                'belvg_layoutcustomizer_layoutblockopentype'
            ];
            foreach ($oldTables as $table) {
                $this->connection->dropTable($this->setup->getTable($table));
            }
        }

        // Drop `main_category` column if exists
        if (version_compare($context->getVersion(), '1.3.1', '<')) {
            $connection = $setup->getConnection();

            // get table
            $layoutTable = $setup->getTable('belvg_layoutcustomizer_layout');

            // column to delete
            $mainCategoryColumn = 'main_category';

            // Check if the table already exists
            if ($connection->tableColumnExists($layoutTable, $mainCategoryColumn) !== false) {
                // del_flg = column name which you want to delete
                $connection->dropColumn($layoutTable, $mainCategoryColumn);
            }
        }

        $installer->endSetup();
    }
}
