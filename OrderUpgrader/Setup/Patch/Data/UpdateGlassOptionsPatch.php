<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */

declare(strict_types=1);

namespace BelVG\OrderUpgrader\Setup\Patch\Data;

use Magento\Framework\DB\Select;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Psr\Log\LoggerInterface;

/**
 * Data patch to update glass option keys
 */
class UpdateGlassOptionsPatch implements DataPatchInterface
{
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Apply data patch
     *
     * @return $this
     * @throws \Exception
     */
    public function apply(): self
    {
        $this->moduleDataSetup->startSetup();

        try {
            $connection = $this->moduleDataSetup->getConnection();

            // Update 2-layer glass options
            $this->updateGlassOptions(
                $connection,
                '2_layer_glass',
                [
                    '%2-lag%',
                    '%2lag%',
                    '%2-layer%',
                    '%2_layer%',
                    '%2layer%',
                    '%2 laery%'
                ]
            );

            // Update 3-layer glass options
            $this->updateGlassOptions(
                $connection,
                '3_layer_glass',
                [
                    '%3-lag%',
                    '%3lag%',
                    '%3-layer%',
                    '%3_layer%',
                    '%3layer%',
                    '%3 laery%'
                ]
            );

            $this->logger->info('Glass options keys have been updated successfully.');
        } catch (\Exception $e) {
            $this->logger->error('Error updating glass options keys: ' . $e->getMessage());
            throw $e;
        }

        $this->moduleDataSetup->endSetup();

        return $this;
    }

    /**
     * Update glass options by pattern
     *
     * @param AdapterInterface $connection
     * @param string $newKeyValue
     * @param array $titlePatterns
     * @return void
     */
    private function updateGlassOptions(
        AdapterInterface $connection,
        string $newKeyValue,
        array $titlePatterns
    ): void {
        // Get the subquery to identify records to update
        $subSelect = $this->buildSelectQuery($connection, $titlePatterns);
        $idsToUpdate = $connection->fetchCol($subSelect);

        if (!empty($idsToUpdate)) {
            $connection->update(
                $connection->getTableName('belvg_mageworx_optiontemplates_group_option_type_key'),
                ['key' => $newKeyValue],
                ['mageworx_option_type_id IN (?)' => $idsToUpdate]
            );
        }
    }

    /**
     * Build select query for finding glass options
     *
     * @param AdapterInterface $connection
     * @param array $titlePatterns
     * @return Select
     */
    private function buildSelectQuery(AdapterInterface $connection, array $titlePatterns): Select
    {
        $select = $connection->select()
            ->from(
                ['mogotk' => $connection->getTableName('belvg_mageworx_optiontemplates_group_option_type_key')],
                ['mageworx_option_type_id']
            )
            ->join(
                ['mogotv' => $connection->getTableName('mageworx_optiontemplates_group_option_type_value')],
                'mogotk.mageworx_option_type_id = mogotv.mageworx_option_type_id',
                []
            )
            ->join(
                ['mogott' => $connection->getTableName('mageworx_optiontemplates_group_option_type_title')],
                'mogott.option_type_id = mogotv.option_type_id AND mogott.store_id = 1',
                []
            )
            ->join(
                ['mogo' => $connection->getTableName('mageworx_optiontemplates_group_option')],
                'mogo.option_id = mogotv.option_id',
                []
            )
            ->join(
                ['mogok' => $connection->getTableName('belvg_mageworx_optiontemplates_group_option_key')],
                'mogok.mageworx_option_id = mogo.mageworx_option_id',
                []
            )
            ->where('mogok.`key` LIKE ?', '%energy_class%');

        // Build OR condition for title patterns
        $conditions = [];
        foreach ($titlePatterns as $pattern) {
            $conditions[] = $connection->quoteInto('mogott.title LIKE ?', $pattern);
        }

        $select->where(implode(' OR ', $conditions));

        return $select;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }
}