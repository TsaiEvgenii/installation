<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023.
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionMadeInDenmark\Plugin\Model\Import\PriceImport;


use BelVG\MageWorxGroupProductCsv\Exceptions\InvalidDataForImport;
use BelVG\MageWorxGroupProductCsv\Model\Import\PriceImport;
use BelVG\MageWorxGroupProductCsv\Model\Import\SqlProcessorManager;
use BelVG\MageWorxGroupProductCsv\Model\Import\Validator\ValidatorAggregator;
use BelVG\MageWorxOptionMadeInDenmark\Model\Import\MadeInDenmarkPrice\RowTransformer;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

class MadeInDenmarkPriceImport
{
    private const LOGGER_PREFIX = 'BelVG_MageWorxOptionMadeInDenmark::MadeInDenmarkPriceImportPlugin: ';
    protected ResourceConnection|\Magento\Framework\DB\Adapter\AdapterInterface $resourceConnection;
    public function __construct(
        protected ValidatorAggregator $rowValidator,
        protected RowTransformer $rowTransformer,
        protected SqlProcessorManager $sqlProcessorManager,
        protected LoggerInterface $logger,
        ResourceConnection $resourceConnection,

    ){
        $this->resourceConnection = $resourceConnection->getConnection('write');
    }
    public function afterDoUpdate(
        PriceImport $source,
        $result,
        $data
    ){
        $header = array_shift($data);
        $transformedRows = [];
        $messages= [];
        $count = 1;
        foreach ($data as $row){
            $row = array_combine($header, $row);
            if(($response = $this->rowValidator->execute($row))
                && $response->isValid() === false){
                $messages = array_merge($messages,\array_merge([__('Error in row %1', $count)],
                    $response->getMessages()));
            }
            $transformedRows = array_merge($transformedRows,
                $this->rowTransformer->transform($row));
            $count++;
        }
        if (sizeof($messages)) {
            throw new InvalidDataForImport($messages);
        }
        $sqlCommands = $this->sqlProcessorManager->generate($transformedRows);
        foreach ($sqlCommands as $sqlCommand){
            $this->executeCommand($sqlCommand);
        }
    }

    /**
     * @throws \Zend_Db_Statement_Exception
     */
    private function executeCommand(string $sqlCommand): void
    {
        try {
            $this->resourceConnection->beginTransaction();
            $query = $this->resourceConnection->query($sqlCommand);
            $query->execute();
            $this->resourceConnection->commit();
        } catch (\Exception $e) {
            $this->resourceConnection->rollBack();
            throw $e;
        }
    }
}