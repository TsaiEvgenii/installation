<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var SchemaSetupInterface
     */
    protected $setup;

    /**
     * @var AdapterInterface
     */
    protected $connection;

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->setup = $setup;
        $this->connection = $setup->getConnection();

        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.1.2', '<')) {
            $setup->getConnection()->dropIndex('belvg_factory_material', 'BELVG_FACTORY_MATERIAL_FACTORY_ID_MATERIAL_ID');
        }
        if (version_compare($context->getVersion(),'1.1.5', '<' )) {
            $this->connection->addColumn(
                $installer->getTable('belvg_factory_store'),
                'email_template',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 256,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Factory email template'
                ]
            );
        }
    }
}
