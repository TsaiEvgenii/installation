<?php
/*
 *  @package Vinduesgrossisten
 *   * @author  Tsai<tsai.evgenii@belvg.com>
 *   * @Copyright
 */

namespace BelVG\MageWorxOptionTemplates\Console\Command;

use Magento\Framework\Validator\Exception as ValidatorException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class Update
 *
 * @package BelVG\MageWorxOptionTemplates\Console\Command
 */
class Update extends Command
{
    /** @var string  */
    const STORE_ID = 'store-id';

    /** @var string[]  */
    const OPTION_TABLES
        = [
            'title' => 'mageworx_optiontemplates_group_option_title',
            'description' => 'mageworx_optiontemplates_group_option_description',
            'warning' => 'mageworx_optiontemplates_group_option_warning'
        ];

    /** @var string[] */
    const OPTION_VALUE_TABLES
        = [
            'title' => 'mageworx_optiontemplates_group_option_type_title',
            'inactive' => 'mageworx_optiontemplates_group_option_type_inactive'
        ];

    /** @var SymfonyStyle */
    protected $io;

    /** @var  */
    protected $storeId;

    /** @var \Magento\Framework\App\ResourceConnection $resourceConnection */
    protected $resourceConnection;

    /** @var \BelVG\ImportProductNames\Helper\Config */
    protected $configHelper;

    /** @var \Magento\Framework\File\Csv */
    protected $csvProcessor;

    /** @var \Magento\Framework\App\State */
    protected $appState;

    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \BelVG\MageWorxOptionTemplates\Helper\Config $configHelper,
        \Magento\Framework\File\Csv $csvProcessor
    ) {
        parent::__construct();
        $this->resourceConnection = $resourceConnection;
        $this->configHelper = $configHelper;
        $this->csvProcessor = $csvProcessor;
        $this->appState = $appState;
    }

    protected function configure()
    {
        $options = [
            new InputOption(
                self::STORE_ID,
                null,
                InputOption::VALUE_REQUIRED,
                'Store ID'
            )
        ];
        $this
            ->setName('belvg:mageworx_option_templates:update')
            ->setDescription('Import CSV file from Stores->Configuration->BELVG->MageWorx Option Templates Update->Import File')
            ->setDefinition($options);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
die('Killed due to https://youtrack.belvgdev.com/issue/SD-4780/MWOT-wrong-price-for-DK-TRAL-TSFKVM3-11#focus=Comments-4-380013.0-0');
        $this->appState->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_ADMINHTML,
            [$this, 'emulationCallback'], [$input, $output]);
    }

    public function emulationCallback(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        try {
            $this->storeId = $input->getOption(self::STORE_ID);
            if (!$this->storeId) {
                $message = '';
                $message .= self::STORE_ID . ' does not set. ';
                throw new ValidatorException(
                    __($message)
                );
            }

            $importCsvFilePath = $this->configHelper->getCsvFilePath();
            $importedData = $this->csvProcessor->getData($importCsvFilePath);
            $columnNames = array_shift($importedData);
            $columnNames = array_map(
                function ($item) {
                    return preg_replace("/[^a-zA-Z0-9_]+/", "", $item);
                },
                $columnNames
            );
            $preparedImportedData = [];
            foreach ($importedData as $importedDatum){
                $preparedImportedData[] = array_combine($columnNames, $importedDatum);
            }

            $sortedPreparedImportedData = [];
            foreach ($preparedImportedData as $preparedImportedDatum){
                if($preparedImportedDatum['option_type'] === 'field'){
                    $sortedPreparedImportedData['fields'][] = $preparedImportedDatum;
                }
                if ($preparedImportedDatum['option_type'] === 'radio') {
                    if ($sortedPreparedImportedData['radios'][$preparedImportedDatum['group_id']][$preparedImportedDatum['option_id']]
                        ?? false) {
                        $sortedPreparedImportedData['radios'][$preparedImportedDatum['group_id']][$preparedImportedDatum['option_id']]['values'][$preparedImportedDatum['option_type_id']]
                            = [
                            'title' => $preparedImportedDatum['value_title'] ?? null,
                            'inactive' => $preparedImportedDatum['inactive'] ?? null,
                            'mageworx_option_type_id' => $preparedImportedDatum['mageworx_option_type_id'] ?? null
                        ];
                    } else {
                        $sortedPreparedImportedData['radios'][$preparedImportedDatum['group_id']][$preparedImportedDatum['option_id']]
                            = [
                            'group_id' => $preparedImportedDatum['group_id'],
                            'group_title' => $preparedImportedDatum['group_title'],
                            'option_type' => $preparedImportedDatum['option_type'],
                            'option_title' => $preparedImportedDatum['option_title'],
                            'option_id' => $preparedImportedDatum['option_id'],
                            'mageworx_option_id' => $preparedImportedDatum['mageworx_option_id'],
                            'option_description' => $preparedImportedDatum['option_description'],
                            'option_warning' => $preparedImportedDatum['option_warning'],
                            'values' => [
                                $preparedImportedDatum['option_type_id'] => [
                                    'title' => $preparedImportedDatum['value_title'] ?? null,
                                    'inactive'=> $preparedImportedDatum['inactive'] ?? null,
                                    'mageworx_option_type_id' => $preparedImportedDatum['mageworx_option_type_id'] ?? null
                                ]
                            ]
                        ];
                    }
                }
            }
            $fields = $sortedPreparedImportedData['fields'] ?? [];
            $radios = $sortedPreparedImportedData['radios'];

            foreach ($fields as $field){
                $this->updateOption($field, 'title');
                $this->updateOption($field, 'description');
                $this->updateOption($field, 'warning');
            }

            foreach ($radios as $radio){
                foreach ($radio as $option){
                    $this->updateOption($option, 'title');
                    $this->updateOption($option, 'description');
                    $this->updateOption($option, 'warning');
                    foreach ($option['values'] as $optionTypeId => $value){
                        if ($value['title'] !== null) {
                            $this->updateOptionValue($optionTypeId, $value['title'], 'title');
                        }

                        if ($value['inactive'] !== null && $value['mageworx_option_type_id']) {
                            $this->updateOptionValue($value['mageworx_option_type_id'], $value['inactive'], 'inactive');
                        }
                    }
                }
            }

            $this->io->success('Done');
            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $this->io->error($e->getMessage());

            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }

    protected function updateOption($fieldData, $dataType)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName(self::OPTION_TABLES[$dataType]);
        if ($this->isOptionExist($fieldData, $dataType)) {
            //update
            if ($dataType === 'title' && (($fieldData['option_title'] ?? '') !== '')) {
                $connection->update(
                    $tableName,
                    ['title' => $fieldData['option_title']],
                    ['store_id = ?' => $this->storeId, 'option_id = ?' => $fieldData['option_id']]
                );
                $this->addNote('Title for option with ID = ' . $fieldData['option_id'] .' was updated with value "' . $fieldData['option_title'] . '"');
            } elseif ($dataType === 'description' && (($fieldData['option_description'] ?? '') !== '')) {
                $connection->update(
                    $tableName,
                    ['description' => $fieldData['option_description']],
                    ['store_id = ?' => $this->storeId, 'mageworx_option_id = ?' => $fieldData['mageworx_option_id']]
                );
                $this->addNote('Description for option with ID = ' . $fieldData['option_id'] .' was updated with value "' . $fieldData['option_description'] . '"');
            } elseif ($dataType === 'warning' && (($fieldData['option_warning'] ?? '') !== '')) {
                $connection->update(
                    $tableName,
                    ['warning' => $fieldData['option_warning']],
                    ['store_id = ?' => $this->storeId, 'mageworx_option_id = ?' => $fieldData['mageworx_option_id']]
                );
                $this->addNote('Warning for option with ID = ' . $fieldData['option_id'] .' was updated with value "' . $fieldData['option_warning'] .'"');
            }
        } else {
            //insert
            if ($dataType === 'title' && (($fieldData['option_title'] ?? '') !== '')) {
                try {
                    $connection->insert($tableName, [
                        'option_id' => $fieldData['option_id'],
                        'store_id' => $this->storeId,
                        'title' => $fieldData['option_title']
                    ]);
                    $this->addNote('Title for option with ID = ' . $fieldData['option_id'] . ' was created with value "'
                        . $fieldData['option_title'] . '"');
                } catch (\Exception $e) {

                }
            } elseif ($dataType === 'description' && (($fieldData['option_description'] ?? '') !== '')) {
                $connection->insert($tableName, [
                    'mageworx_option_id' => $fieldData['mageworx_option_id'],
                    'store_id' => $this->storeId,
                    'description' => $fieldData['option_description']]);
                $this->addNote('Description for option with ID = ' . $fieldData['option_id'] .' was created with value "' . $fieldData['option_description'] . '"');
            } elseif ($dataType === 'warning' && (($fieldData['option_warning'] ?? '') !== '')) {
                $connection->insert($tableName, [
                    'mageworx_option_id' => $fieldData['mageworx_option_id'],
                    'store_id' => $this->storeId,
                    'warning' => $fieldData['option_warning']]);
                $this->addNote('Warning for option with ID = ' . $fieldData['option_id'] .' was created with value "' . $fieldData['option_warning'] . '"');
            }
        }
    }

    protected function isOptionExist($optionData, $optionType = 'title')
    {
        $connection = $this->resourceConnection->getConnection();
        $mainTableName = $connection->getTableName(self::OPTION_TABLES[$optionType]);
        $select = $connection->select()
            ->from($mainTableName)
            ->where('store_id = ?', $this->storeId);
        if ($optionType === 'title') {
            $select->where('option_id = ?', $optionData['option_id']);
        } else {
            $select->where('mageworx_option_id = ?', $optionData['mageworx_option_id']);
        }
        $result = $connection->fetchOne($select);

        return $result;
    }

    protected function updateOptionValue($optionTypeId, $value, $optionValueType)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName(self::OPTION_VALUE_TABLES[$optionValueType]);
        $noteMessage = ucfirst($optionValueType) . ' for option value with ID = ' . $optionTypeId . ' with value "'
            . $value . '"';
        if ($optionValueType === 'title') {
            if ($this->isOptionValueExist($optionTypeId, $optionValueType)) {
                //update
                $connection->update(
                    $tableName,
                    ['title' => $value],
                    ['store_id = ?' => $this->storeId, 'option_type_id = ?' => $optionTypeId]
                );
                $this->addNote($noteMessage . ' was updated');

            } else {
                //insert
                try {
                    $connection->insert($tableName, [
                        'option_type_id' => $optionTypeId,
                        'store_id' => $this->storeId,
                        'title' => $value
                    ]);
                    $this->addNote($noteMessage . ' was created');
                } catch (\Exception $e) {
                    $this->addError('Error for ' . $noteMessage . ' | ' . $e->getMessage());
                }
            }
        }

        if($optionValueType === 'inactive'){
            if ($this->isOptionValueExist($optionTypeId, $optionValueType)) {
                //update
                $connection->update(
                    $tableName,
                    ['inactive' => $value],
                    ['store_id = ?' => $this->storeId, 'mageworx_option_type_id = ?' => $optionTypeId]
                );
                $this->addNote($noteMessage . ' was updated');

            } else {
                //insert
                try {
                    $connection->insert($tableName, [
                        'mageworx_option_type_id' => $optionTypeId,
                        'store_id' => $this->storeId,
                        'inactive' => $value
                    ]);
                    $this->addNote($noteMessage . ' was created');
                } catch (\Exception $e) {
                    $this->addError('Error for ' . $noteMessage . ' | ' . $e->getMessage());
                }
            }

        }
    }

    protected function isOptionValueExist($optionTypeId, $optionType = 'title')
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName(self::OPTION_VALUE_TABLES[$optionType]);
        $select = $connection->select()
            ->from($tableName)
            ->where('store_id = ?', $this->storeId);
        if ($optionType === 'inactive') {
            $select->where('mageworx_option_type_id = ?', $optionTypeId);
        } else {
            $select->where('option_type_id = ?', $optionTypeId);
        }

        $result = $connection->fetchOne($select);

        return $result;
    }

    protected function addNote($message)
    {
        $this->io->note($message);
    }

    protected function addError($message)
    {
        $this->io->error($message);
    }
}
