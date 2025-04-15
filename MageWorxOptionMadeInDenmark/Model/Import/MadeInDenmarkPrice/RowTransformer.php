<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023.
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionMadeInDenmark\Model\Import\MadeInDenmarkPrice;


use BelVG\MadeInDenmark\Model\Config;
use BelVG\MageWorxGroupProductCsv\Model\DataAdapter\PriceOptionDataAdapter;
use BelVG\MageWorxGroupProductCsv\Model\DataAdapter\StoreProcessorPrice;
use Magento\Store\Model\StoreManagerInterface;

class RowTransformer
{
    const OPTIN_ID = PriceOptionDataAdapter::ID;

    /**
     * RowTransformer constructor.
     *
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        protected StoreManagerInterface $storeManager,
        protected Config $madeInDenmarkConfig
    ) {
    }

    public function transform(array $row): array
    {
        $transformedRows = [];
        foreach ($this->getAssignedFields() as $key => $storeId) {
            if (\array_key_exists($key, $row) && !empty($row[$key]) && trim($row[$key]) !== '') {
                $transformedRows[] = [
                    'price'           => (float)$row[$key],
                    'id'              => $row[self::OPTIN_ID],
                    'store_id'        => $storeId,
                    'identifier_type' => 'made_in_denmark_' . $row['identifier_type'],
                    'price_type'      => $row['Price type*'],
                ];
            }
        }
        return $transformedRows;
    }

    public function getAssignedFields(): array
    {
        $assignedStores = [];
        $stores = $this->storeManager->getStores(true);
        $stores = \array_filter($stores, function ($store) {
            return 0 !== (int)$store->getId();
        });
        foreach ($stores as $store) {
            $assignedStores[StoreProcessorPrice::getFieldTemplate('made_in_denmark_price', $store->getName())]
                = (int)$store->getId();
        }
        $assignedStores[StoreProcessorPrice::getFieldTemplate('made_in_denmark_price', 'default')] = 0;
        return $assignedStores;
    }

}
