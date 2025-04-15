<?php
/**
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\MageWorxOptionTemplates\Console\Command;

use Magento\Store\Model\Store as StoreModel;
use Magento\Framework\App;
use Magento\Framework\App\ResourceConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Check inconsistency between products and MWOT
 *
 * Class HealthCheck
 * @package BelVG\MageWorxOptionTemplates\Console\Command
 */
class HealthCheck extends Command
{
    const PROD_ID = 'prod-id';
    const MWOT_ID = 'mwot-id';
    const STORE_IDS = 'store-ids';
    const COLORS = 'colors';
    const MULTIPLE_DELIMETER = ',';

    protected $appState;
    protected $resource;

    private $unnecessaryGroupValuesCheckPassed = [];
    private $unnecessaryOrMissedGroupsCheckPassed = [];
    private $inconsistencyErrors = [];
    private $io;

    public function __construct(
        App\State\Proxy $appState,
        ResourceConnection\Proxy $resourceConnection
    ) {
        $this->appState = $appState;
        $this->resource = $resourceConnection;

        parent::__construct();
    }

    protected function configure()
    {
        $options = [
            new InputOption(
                self::PROD_ID,
                null,
                InputOption::VALUE_OPTIONAL,
                'Make check only for certain Product ID'
            ),
            new InputOption(
                self::MWOT_ID,
                null,
                InputOption::VALUE_OPTIONAL,
                'Make check only for certain MWOT ID'
            ),
            new InputOption(
                self::STORE_IDS,
                null,
                InputOption::VALUE_OPTIONAL,
                'Make check only for certain Store IDs'
            ),
            new InputOption(
                self::COLORS,
                'c',
                InputOption::VALUE_OPTIONAL,
                'Make check only for certain Color. Example --colors=without
                            Possible values:
                            - without (without colors)
                            - inside (inside color)
                            - outside (outside color)
                            - both (inside and outside colors)'
            )
        ];

        $this
            ->setName('belvg:mageworx:health_check')
            ->setDescription('Check inconsistency between products and MWOT')
            ->setDefinition($options);
    }

    protected function getTemplates(int $productId, int $mwotId): iterable
    {
        $sql = 'SELECT mwot_group.`group_id`, mwot_group.`title` as group_title,
                    mwot_relation.`product_id`
            FROM ' . $this->resource->getTableName('mageworx_optiontemplates_group') . ' mwot_group
            LEFT JOIN ' . $this->resource->getTableName('mageworx_optiontemplates_relation') . ' mwot_relation
                ON (mwot_group.`group_id` = mwot_relation.`group_id`)
            WHERE 1 '
                . ($productId != 0 ? ' AND mwot_relation.product_id = ' . (int)$productId : '')
                . ($mwotId != 0 ? ' AND mwot_group.group_id = ' . (int)$mwotId : '');
        //AND mwot_group.`group_id` > 3

        return $this->resource->getConnection()->fetchAll($sql);
    }

    protected function getGroupOptions(int $groupId, iterable $storeIds, string $colors): iterable
    {
        $sql = 'SELECT group_option.`group_id`, group_option.`option_id`, group_option.`mageworx_option_id`,
                    group_option.`inside_outside_color`,
                    group_option_title.`title` as option_title, group_option_title.`store_id`
            FROM ' . $this->resource->getTableName('mageworx_optiontemplates_group_option') . ' group_option
            JOIN ' . $this->resource->getTableName('mageworx_optiontemplates_group_option_title') . ' group_option_title
                ON (group_option.`option_id` = group_option_title.`option_id`)
            WHERE group_id = ' . (int)$groupId
                . (!empty($storeIds) ? ' AND group_option_title.store_id IN (' . implode(self::MULTIPLE_DELIMETER, $storeIds) . ')' : '')
        . $this->getColorsCondition($colors);

        return $this->resource->getConnection()->fetchAll($sql);
    }

    /**
     * @param string $colors
     * @return string
     */
    protected function getColorsCondition(string $colors): string
    {
        $condition = ' AND group_option.`inside_outside_color`';
        switch ($colors) {
            case 'without':
                $condition .= " IS NULL";
                break;
            case 'outside':
            case 'inside':
                $condition .= " = '$colors'";
                break;
            case 'both':
                $condition .= " IS NOT NULL";
                break;
            default:
                $condition = '';
                break;
        }
        return $condition;
    }

    protected function getProductOptionsByMWOption(string $mageworxOptionId, int $storeId, $productId = 0): iterable
    {
        $sql = 'SELECT prod_option.mageworx_group_option_id, prod_option.product_id, prod_option.option_id,
                    prod_option.`inside_outside_color`,
                    prod_option_title.title as option_title, prod_option_title.`store_id`
                FROM ' . $this->resource->getTableName('catalog_product_option') . ' prod_option
                JOIN ' . $this->resource->getTableName('catalog_product_option_title') . ' prod_option_title
                    ON (prod_option.`option_id` = prod_option_title.`option_id`)
                WHERE `mageworx_group_option_id` = "' . $mageworxOptionId . '"
                    AND prod_option_title.`store_id` = ' . (int)$storeId
                    . ($productId != 0 ? ' AND prod_option.product_id = ' . (int)$productId : '');

        return (array)$this->resource->getConnection()->fetchRow($sql);
    }

    private function getCompareErrors(iterable $columnsToCheck, $mwRow, $prodRow): iterable
    {
        $validationErrors = [];

        foreach ($columnsToCheck as $columnToCheck) {
            if (empty($mwRow[$columnToCheck]) && !isset($prodRow[$columnToCheck])) {
                continue; //case like column "inside_outside_color" should be "" actual result is "[NOT EXISTS]"
            }

            if (!isset($prodRow[$columnToCheck]) || $mwRow[$columnToCheck] != $prodRow[$columnToCheck]) {
                $validationErrors[] = [
                    'column' => $columnToCheck,
                    'should_be' => $mwRow[$columnToCheck],
                    'actual' => isset($prodRow[$columnToCheck]) ? $prodRow[$columnToCheck] : '[NOT EXISTS]'
                ];
            }
        }
        unset($columnToCheck);

        return $validationErrors;
    }

    protected function compareOptions(iterable $mwOption, iterable $productOption): iterable
    {
        $columnsToCheck = [
            'option_title',
            'inside_outside_color',
        ];

        return $this->getCompareErrors($columnsToCheck, $mwOption, $productOption);
    }

    protected function compareOptionValues(iterable $mwOptionValue, iterable $productOptionValue): iterable
    {
        $columnsToCheck = [
            'option_value_title',
            'price',
            'price_type',
        ];

        return $this->getCompareErrors($columnsToCheck, $mwOptionValue, $productOptionValue);
    }

    protected function showOptionErrors(
        iterable $optionsCompareIssues,
        iterable $template,
        iterable $mwOption
    ): void {
        if (!empty($optionsCompareIssues)) {
            foreach ($optionsCompareIssues as $optionIssue) {
                $msg = sprintf(
                    '[option issue]: Template "%s" (ID=%s), Product ID="%s", Store ID="%s", column "%s" should be "%s" actual result is "%s"',
                    $template['group_title'],
                    $template['group_id'],
                    $template['product_id'],
                    $mwOption['store_id'],
                    $optionIssue['column'],
                    $optionIssue['should_be'],
                    $optionIssue['actual']
                );

                $this->io->error($msg);
                $this->inconsistencyErrors[] = $msg;
            }
            unset($optionIssue);
        }
    }

    protected function showOptionValueErrors(
        iterable $optionValuesCompareIssues,
        iterable $template,
        iterable $mwOption
    ): void {
        if (!empty($optionValuesCompareIssues)) {
            foreach ($optionValuesCompareIssues as $optionValueIssue) {
                $msg = sprintf(
                    '[option value issue]: Template "%s" (ID=%s), Product ID="%s", Store ID="%s", Option="%s" (ID=%s), column "%s" should be "%s" actual result is "%s"',
                    $template['group_title'],
                    $template['group_id'],
                    $template['product_id'],
                    $mwOption['store_id'],
                    $mwOption['option_title'],
                    $mwOption['group_id'],
                    $optionValueIssue['column'],
                    $optionValueIssue['should_be'],
                    $optionValueIssue['actual']
                );

                $this->io->error($msg);
                $this->inconsistencyErrors[] = $msg;
            }
            unset($optionIssue);
        }
    }

    protected function showUnnecessaryOrMissedGroupOptionsErrors(iterable $issues): void {
        if (!empty($issues)) {
            foreach ($issues as $issueType => $issue) {
                foreach ($issue as $issueDetails) {
                    $msg = sprintf(
                        '[%s]: Product ID="%s", Group "%s" (MWOT ID=%s)',
                        $issueType,
                        $issueDetails['product_id'],
                        $issueDetails['option_title'],
                        isset($issueDetails['group_id']) ? $issueDetails['group_id'] : '-'
                    );
                    $this->io->error($msg);
                    $this->inconsistencyErrors[] = $msg;
                }
                unset($issueDetails);
            }
            unset($issueType);
            unset($issue);
        }
    }

    protected function showUnnecessaryOptionValueErrors(iterable $issues, iterable $template): void {
        if (!empty($issues)) {
            foreach ($issues as $issue) {
                $msg = sprintf(
                    '[option value unnecessary]: Product ID="%s", Group "%s" (MWOT ID=%s), Option "%s" has unnecessary value "%s"',
                    $issue['product_id'],
                    $template['group_title'],
                    $template['group_id'],
                    $issue['option_title'],
                    $issue['option_value_title']
                );
                $this->io->error($msg);
                $this->inconsistencyErrors[] = $msg;
            }
            unset($issue);
        }
    }

    protected function getGroupOptionValues(string $mageworxOptionId, int $storeId): iterable
    {
        $sql = 'SELECT mwot_option.`mageworx_option_id`, mwot_option.`option_id`,
                mwot_option_value.`option_type_id`, mwot_option_value.`mageworx_option_type_id`,
                mwot_option_value_title.`store_id`, mwot_option_value_title.`title` as option_value_title,
                mwot_is_default.is_default,
                mwot_value_price.price as price, mwot_value_price.price_type as price_type
            FROM `' . $this->resource->getTableName('mageworx_optiontemplates_group_option') . '` mwot_option
            JOIN `' . $this->resource->getTableName('mageworx_optiontemplates_group_option_type_value') . '` mwot_option_value
                ON (mwot_option.`option_id` = mwot_option_value.`option_id`)
            JOIN `' . $this->resource->getTableName('mageworx_optiontemplates_group_option_type_price') . '` mwot_value_price
		        ON (mwot_option_value.option_type_id = mwot_value_price.option_type_id AND mwot_value_price.store_id = ' . (int)$storeId . ')
            JOIN `' . $this->resource->getTableName('mageworx_optiontemplates_group_option_type_title') . '` mwot_option_value_title
                ON (mwot_option_value.`option_type_id` = mwot_option_value_title.`option_type_id`)
            JOIN `' . $this->resource->getTableName('mageworx_optiontemplates_group_option_type_is_default') . '` mwot_is_default
                ON (mwot_option_value.`mageworx_option_type_id` = mwot_is_default.`mageworx_option_type_id`)
            WHERE mwot_option.`mageworx_option_id` = "' . $mageworxOptionId . '"' .
                ' AND mwot_option_value_title.store_id = ' . (int)$storeId .
                ' AND mwot_is_default.store_id = ' . (int)$storeId;

        return (array)$this->resource->getConnection()->fetchAll($sql);
    }

    protected function getProductOptionValuesByMWOptionValue(string $mageworxOptionTypeId, int $storeId, $productId = 0): iterable
    {
        $sql = 'SELECT product_option.product_id, product_option.mageworx_group_option_id, product_option.inside_outside_color,
                product_option_title.option_title_id, product_option_title.title as option_title,
                product_option_value.option_type_id, product_option_value.mageworx_option_type_id,
                product_option_value.mageworx_optiontemplates_group_option_type_id, product_option_value_title.store_id,
                product_option_value_title.title as option_value_title,
                prod_value_price.price as price, prod_value_price.price_type as price_type
            FROM ' . $this->resource->getTableName('catalog_product_option') . ' product_option
            JOIN ' . $this->resource->getTableName('catalog_product_option_title') . ' product_option_title
                ON (product_option.option_id = product_option_title.option_id)
            JOIN ' . $this->resource->getTableName('catalog_product_option_type_value') . ' product_option_value
                ON (product_option.option_id = product_option_value.`option_id`)
            JOIN ' . $this->resource->getTableName('catalog_product_option_type_title') . ' product_option_value_title
                ON (product_option_value.option_type_id = product_option_value_title.option_type_id)
            LEFT JOIN ' . $this->resource->getTableName('catalog_product_option_type_price') . ' prod_value_price
                ON (product_option_value.`option_type_id` = prod_value_price.`option_type_id` AND prod_value_price.store_id = ' . (int)$storeId . ')
            WHERE product_option_title.store_id = ' . (int)$storeId . '
               AND product_option_value_title.store_id = ' . (int)$storeId . '
               AND product_option_value.mageworx_optiontemplates_group_option_type_id = "' . $mageworxOptionTypeId . '"'
            . ($productId != 0 ? ' AND product_option.product_id = ' . (int)$productId : '');

        return (array)$this->resource->getConnection()->fetchRow($sql);
    }

    protected function checkUnnecessaryGroupOptionsForProduct(int $productId): iterable
    {
        $sql = 'SELECT prod_option.product_id, prod_option.mageworx_group_option_id,
                prod_option_title.title as option_title,
                mwot_group.title as group_title,
                mwot_relation.group_id
            FROM ' . $this->resource->getTableName('catalog_product_option') . ' prod_option
            JOIN catalog_product_option_title prod_option_title
                ON (prod_option.`option_id` = prod_option_title.`option_id`)
            LEFT JOIN ' . $this->resource->getTableName('mageworx_optiontemplates_group_option') . ' as mwot_group_option
                ON (mwot_group_option.mageworx_option_id = prod_option.mageworx_group_option_id)
            LEFT JOIN ' . $this->resource->getTableName('mageworx_optiontemplates_group') . ' as mwot_group
                ON (mwot_group.group_id = mwot_group_option.group_id)
            LEFT JOIN ' . $this->resource->getTableName('mageworx_optiontemplates_relation') . ' as mwot_relation
                ON (mwot_relation.group_id = mwot_group.`group_id` and mwot_relation.product_id = prod_option.product_id)
            WHERE prod_option.`product_id` = ' . (int)$productId . '
                AND prod_option_title.store_id = ' . (int)StoreModel::DEFAULT_STORE_ID . '
                AND mwot_relation.group_id IS NULL';

        return (array)$this->resource->getConnection()->fetchAll($sql);
    }

    protected function checkMissedGroupOptionsForProduct(int $productId): iterable
    {
        $sql = 'SELECT mwot_relation.product_id,
                mwot_group.title, mwot_group.group_id,
                mwot_group_option.mageworx_option_id,
                mwot_group_option_title.title as option_title,
                prod_option.option_id
            FROM ' . $this->resource->getTableName('mageworx_optiontemplates_relation') . ' mwot_relation
            JOIN ' . $this->resource->getTableName('mageworx_optiontemplates_group') . ' mwot_group
                ON (mwot_relation.group_id = mwot_group.group_id)
            JOIN ' . $this->resource->getTableName('mageworx_optiontemplates_group_option') . ' mwot_group_option
                ON (mwot_group.group_id = mwot_group_option.group_id)
            JOIN ' . $this->resource->getTableName('mageworx_optiontemplates_group_option_title') . ' mwot_group_option_title
                ON (mwot_group_option.`option_id` = mwot_group_option_title.`option_id`)
            LEFT JOIN ' . $this->resource->getTableName('catalog_product_option') . ' prod_option
                ON (prod_option.mageworx_group_option_id = mwot_group_option.mageworx_option_id and prod_option.`product_id` = ' . (int)$productId . ')
            WHERE mwot_relation.`product_id` = ' . (int)$productId . '
                AND mwot_group_option_title.store_id = ' . (int)StoreModel::DEFAULT_STORE_ID . '
                AND prod_option.option_id IS NULL';

        return (array)$this->resource->getConnection()->fetchAll($sql);
    }

    protected function checkGroupOptionsForProduct(int $productId): iterable
    {
        if (in_array($productId, $this->unnecessaryOrMissedGroupsCheckPassed)) {
            return [];
        }

        $unnecessaryGroups = $this->checkUnnecessaryGroupOptionsForProduct($productId);
        $missedGroups = $this->checkMissedGroupOptionsForProduct($productId);

        $this->unnecessaryOrMissedGroupsCheckPassed[] = $productId;

        return [
            'unnecessaryGroup' => $unnecessaryGroups,
            'missedGroup' => $missedGroups
        ];
    }

    protected function checkUnnecessaryGroupOptionValuesForProduct(
        iterable $mwOption,
        int $productId
    ): iterable {
        $key = implode('-', [$productId, $mwOption['mageworx_option_id']]);
        if (in_array($key, $this->unnecessaryGroupValuesCheckPassed)) {
            return [];
        }

        $sql = 'SELECT prod_option.`product_id`, prod_option.`mageworx_group_option_id`,
	            prod_option_title.title as option_title,
                prod_option_value.`option_id`, prod_option_value.`mageworx_optiontemplates_group_option_type_id`,
                prod_option_value_title.title as option_value_title
            FROM ' . $this->resource->getTableName('catalog_product_option') . ' prod_option
            JOIN ' . $this->resource->getTableName('catalog_product_option_title') . ' prod_option_title
                ON (prod_option.`option_id` = prod_option_title.`option_id`)
            JOIN ' . $this->resource->getTableName('catalog_product_option_type_value') . ' prod_option_value
                ON (prod_option.`option_id` = prod_option_value.`option_id`)
            JOIN ' . $this->resource->getTableName('catalog_product_option_type_title') . ' prod_option_value_title
                ON (prod_option_value.`option_type_id` = prod_option_value_title.`option_type_id`)
            WHERE prod_option.`product_id` = ' . (int)$productId . '
               AND prod_option.`mageworx_group_option_id` = "' . $mwOption['mageworx_option_id'] . '"
               AND prod_option_value_title.store_id = ' . (int)StoreModel::DEFAULT_STORE_ID . '
               AND prod_option_title.store_id = ' . (int)StoreModel::DEFAULT_STORE_ID . '
               AND prod_option_value.`mageworx_optiontemplates_group_option_type_id` IS NULL';

        $this->unnecessaryGroupValuesCheckPassed[] = $key;

        return (array)$this->resource->getConnection()->fetchAll($sql);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->appState->emulateAreaCode(
            App\Area::AREA_ADMINHTML,
            [$this, 'emulationCallback'], [$input, $io]);
    }

    public function emulationCallback(InputInterface $input, OutputInterface $output)
    {
        $productId = (int)$input->getOption(self::PROD_ID);
        $mwotId = (int)$input->getOption(self::MWOT_ID);
        $storeIds = $input->getOption(self::STORE_IDS);
        $colors = (string) $input->getOption(self::COLORS);

        if ($storeIds != '') {
            $storeIds = explode(self::MULTIPLE_DELIMETER, $storeIds);
        } else {
            $storeIds = [];
        }

        $this->io = new SymfonyStyle($input, $output);

        $templates = $this->getTemplates($productId, $mwotId);
        $tmp_i = 0;
        foreach ($templates as $template) {
            $tmp_i++;
            if (!$template['product_id']) {
                $this->io->note(sprintf('Template iteration #%s: group is not used, title "%s"', $tmp_i, $template['group_title']));
                continue;
            }

            $this->io->comment(sprintf('Template iteration #%s: group title "%s" (ID=%s), productID="%s"', $tmp_i, $template['group_title'], $template['group_id'], $template['product_id']));
            $mwOptions = $this->getGroupOptions($template['group_id'], $storeIds, $colors);
            foreach ($mwOptions as $mwOption) {
                $productOption = $this->getProductOptionsByMWOption($mwOption['mageworx_option_id'], $mwOption['store_id'], $template['product_id']);
                $optionsCompareIssues = $this->compareOptions($mwOption, $productOption);
                $this->showOptionErrors($optionsCompareIssues, $template, $mwOption);

                $mwOptionValues = $this->getGroupOptionValues($mwOption['mageworx_option_id'], $mwOption['store_id']);
                foreach ($mwOptionValues as $mwOptionValue) {
                    $productOptionValue = $this->getProductOptionValuesByMWOptionValue(
                        $mwOptionValue['mageworx_option_type_id'],
                        $mwOptionValue['store_id'],
                        $template['product_id']
                    );

                    $optionValuesCompareIssues = $this->compareOptionValues($mwOptionValue, $productOptionValue);
                    $this->showOptionValueErrors($optionValuesCompareIssues, $template, $mwOption);
                }
                unset($mwOptionValue);

                $productUnnecessaryGroupOptionValues = $this->checkUnnecessaryGroupOptionValuesForProduct($mwOption, $template['product_id']);
                $this->showUnnecessaryOptionValueErrors($productUnnecessaryGroupOptionValues, $template);
            }
            unset($mwOption);

            $productIssues = $this->checkGroupOptionsForProduct($template['product_id']);
            $this->showUnnecessaryOrMissedGroupOptionsErrors($productIssues);

        }
        unset($template);

        if (count($this->inconsistencyErrors)) {
            $this->io->error(sprintf('%s error(s) found :(', count($this->inconsistencyErrors)));
        } else {
            $this->io->success('No errors found :)');
        }

    }
}
