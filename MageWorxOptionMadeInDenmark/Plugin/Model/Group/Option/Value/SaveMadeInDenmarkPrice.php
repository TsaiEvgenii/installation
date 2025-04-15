<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023-2023.
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionMadeInDenmark\Plugin\Model\Group\Option\Value;


use Magento\Catalog\Helper\Data;
use Magento\Directory\Model\Currency;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\OptionTemplates\Model\ResourceModel\Group\Option\Value;
use Magento\Directory\Model\CurrencyFactory;

class SaveMadeInDenmarkPrice
{
    public function __construct(
        protected Value $resource,
        protected FormatInterface $format,
        protected Data $dataHelper,
        protected ScopeConfigInterface $config,
        protected StoreManagerInterface $storeManager,
        protected CurrencyFactory $currencyFactory,
    ){

    }
    public function afterAfterSave(
        $subject,
    ): void {
        $this->saveValueMadeInDenmarkPrices($subject);
    }

    private function saveValueMadeInDenmarkPrices(AbstractModel $object): void
    {
        $objectPrice = $object->getData('made_in_denmark_price');
        $priceTable = $this->resource->getTable('mageworx_optiontemplates_group_option_type_made_in_denmark_price');
        $formattedPrice = $this->format->getNumber($objectPrice);
        $price = (double)sprintf('%F', $formattedPrice);
        $priceType = $object->getPriceType();

        if (isset($objectPrice) && $priceType) {
            //save for store_id = 0
            $select = $this->resource->getConnection()->select()->from(
                $priceTable,
                'option_type_id'
            )->where(
                'option_type_id = ?',
                (int)$object->getId()
            )->where(
                'store_id = ?',
                Store::DEFAULT_STORE_ID
            );
            $optionTypeId = $this->resource->getConnection()->fetchOne($select);

            if ($optionTypeId) {
                if ($object->getStoreId() == '0' || $this->dataHelper->isPriceGlobal()) {
                    $bind = ['price' => $price, 'price_type' => $priceType];
                    $where = [
                        'option_type_id = ?' => $optionTypeId,
                        'store_id = ?' => Store::DEFAULT_STORE_ID,
                    ];

                    $this->resource->getConnection()->update($priceTable, $bind, $where);
                }
            } else {
                $bind = [
                    'option_type_id' => (int)$object->getId(),
                    'store_id' => Store::DEFAULT_STORE_ID,
                    'price' => $price,
                    'price_type' => $priceType,
                ];
                $this->resource->getConnection()->insert($priceTable, $bind);
            }
        }

        $scope = (int)$this->config->getValue(
            Store::XML_PATH_PRICE_SCOPE,
            ScopeInterface::SCOPE_STORE
        );

        if ($scope == Store::PRICE_SCOPE_WEBSITE
            && $priceType
            && isset($objectPrice)
            && $object->getStoreId() != Store::DEFAULT_STORE_ID
        ) {
            $website  = $this->storeManager->getStore($object->getStoreId())->getWebsite();

            $websiteBaseCurrency = $this->config->getValue(
                Currency::XML_PATH_CURRENCY_BASE,
                ScopeInterface::SCOPE_WEBSITE,
                $website
            );

            $storeIds = $website->getStoreIds();
            if (is_array($storeIds)) {
                foreach ($storeIds as $storeId) {
                    if ($priceType == 'fixed') {
                        $storeCurrency = $this->storeManager->getStore($storeId)->getBaseCurrencyCode();
                        /** @var $currencyModel Currency */
                        $currencyModel = $this->currencyFactory->create();
                        $currencyModel->unsetData('rate');
                        $currencyModel->setData('currency_code', $websiteBaseCurrency);
                        $rate = $currencyModel->getRate($storeCurrency);
                        if (!$rate) {
                            $rate = 1;
                        }
                        $newPrice = $price * $rate;
                    } else {
                        $newPrice = $price;
                    }

                    $select = $this->resource->getConnection()->select()->from(
                        $priceTable,
                        'option_type_id'
                    )->where(
                        'option_type_id = ?',
                        (int)$object->getId()
                    )->where(
                        'store_id = ?',
                        (int)$storeId
                    );
                    $optionTypeId = $this->resource->getConnection()->fetchOne($select);

                    if ($optionTypeId) {
                        $bind = ['price' => $newPrice, 'price_type' => $priceType];
                        $where = ['option_type_id = ?' => (int)$optionTypeId, 'store_id = ?' => (int)$storeId];

                        $this->resource->getConnection()->update($priceTable, $bind, $where);
                    } else {
                        $bind = [
                            'option_type_id' => (int)$object->getId(),
                            'store_id' => (int)$storeId,
                            'price' => $newPrice,
                            'price_type' => $priceType,
                        ];

                        $this->resource->getConnection()->insert($priceTable, $bind);
                    }
                }
            }
        } else {
            if ($scope == Store::PRICE_SCOPE_WEBSITE
                && !isset($objectPrice)
                && !$priceType
            ) {
                $storeIds = $this->storeManager->getStore($object->getStoreId())->getWebsite()->getStoreIds();
                foreach ($storeIds as $storeId) {
                    $where = [
                        'option_type_id = ?' => (int)$object->getId(),
                        'store_id = ?' => $storeId,
                    ];
                    $this->resource->getConnection()->delete($priceTable, $where);
                }
            }
        }

    }
}