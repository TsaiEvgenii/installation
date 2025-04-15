<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023.
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionMadeInDenmark\Ui\DataProvider\Product\Form\Modifier;


use Magento\Catalog\Model\Config\Source\Product\Options\Price as ProductOptionsPrice;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\OptionBase\Ui\DataProvider\Product\Form\Modifier\ModifierInterface;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Psr\Log\LoggerInterface;

class MadeInDenmarkPrice extends AbstractModifier implements ModifierInterface
{
    private const LOGGER_PREFIX = 'BelVG_MageWorxOptionMadeInDenmark::MadeInDenmarkPriceDataProviderModifier: ';
    public const VALUE_KEY_MADE_IN_DENMARK_PRICE = 'made_in_denmark_price';
    private const VALUE_SORT_ORDER_MADE_IN_DENMARK_PRICE = 18;
    protected string $form = 'mageworx_optiontemplates_group_form';
    protected array $meta = [];

    public function __construct(
        protected Http $request,
        protected StoreManagerInterface $storeManager,
        protected ProductOptionsPrice $productOptionsPrice,
        protected LoggerInterface $logger
    ) {
    }

    public function modifyData(array $data): array
    {
        return $data;
    }

    public function modifyMeta(array $meta): array
    {
        if ($this->request->getRouteName() !== 'mageworx_optiontemplates') {
            return $meta;
        }

        $this->meta = $meta;
        $this->addMadeInDenmarkPriceField();
        $this->addMadeInDenmarkPriceIndexToPriceTypeComponent();

        return $this->meta;
    }
    private function addMadeInDenmarkPriceIndexToPriceTypeComponent(): void
    {
        $this->meta[CustomOptions::GROUP_CUSTOM_OPTIONS_NAME]['children']
        [CustomOptions::GRID_OPTIONS_NAME]['children']['record']['children']
        [CustomOptions::CONTAINER_OPTION]['children']
        [CustomOptions::CONTAINER_TYPE_STATIC_NAME]['children']
        [CustomOptions::FIELD_PRICE_TYPE_NAME]['arguments']['data']['config']['imports']['madeInDenmarkPriceIndex']
            ??= self::VALUE_KEY_MADE_IN_DENMARK_PRICE;
    }

    public function isProductScopeOnly(): bool
    {
        return false;
    }

    private function addMadeInDenmarkPriceField(): void
    {
        try {
            $valueMadeInDenmarkFieldConfig = $this->getValueMadeInDenmarkFieldConfig();
            $this->meta[CustomOptions::GROUP_CUSTOM_OPTIONS_NAME]['children']
            [CustomOptions::GRID_OPTIONS_NAME]['children']['record']['children']
            [CustomOptions::CONTAINER_OPTION]['children']
            [CustomOptions::GRID_TYPE_SELECT_NAME]['children']['record']['children']
                = array_replace_recursive(
                $valueMadeInDenmarkFieldConfig,
                $this->meta[CustomOptions::GROUP_CUSTOM_OPTIONS_NAME]['children']
                [CustomOptions::GRID_OPTIONS_NAME]['children']['record']['children']
                [CustomOptions::CONTAINER_OPTION]['children']
                [CustomOptions::GRID_TYPE_SELECT_NAME]['children']['record']['children'],
            );

            $valueMadeInDenmarkStaticFieldConfig = $this->getValueMadeInDenmarkStaticFieldConfig();
            $this->meta[CustomOptions::GROUP_CUSTOM_OPTIONS_NAME]['children']
            [CustomOptions::GRID_OPTIONS_NAME]['children']['record']['children']
            [CustomOptions::CONTAINER_OPTION]['children']
            [CustomOptions::CONTAINER_TYPE_STATIC_NAME]['children']
                = array_replace_recursive(
                $this->meta[CustomOptions::GROUP_CUSTOM_OPTIONS_NAME]['children']
                [CustomOptions::GRID_OPTIONS_NAME]['children']['record']['children']
                [CustomOptions::CONTAINER_OPTION]['children']
                [CustomOptions::CONTAINER_TYPE_STATIC_NAME]['children'],
                $valueMadeInDenmarkStaticFieldConfig
            );
        } catch (NoSuchEntityException $e) {
            $this->logger->error(self::LOGGER_PREFIX . $e->getMessage());
        }
    }

    /**
     * @throws NoSuchEntityException
     */
    private function getValueMadeInDenmarkFieldConfig(): array
    {
        return [
            self::VALUE_KEY_MADE_IN_DENMARK_PRICE => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label'         => __('Made in Denmark Price'),
                            'componentType' => Field::NAME,
                            'component'     => 'MageWorx_OptionLink/js/components/disable-field-handler',
                            'template'      => 'Magento_Catalog/form/field',
                            'formElement'   => Input::NAME,
                            'dataScope'     => self::VALUE_KEY_MADE_IN_DENMARK_PRICE,
                            'dataType'      => Number::NAME,
                            'addbefore'     => $this->getCurrencySymbol(),
                            'addbeforePool' => $this->productOptionsPrice->prefixesToOptionArray(),
                            'sortOrder'     => self::VALUE_SORT_ORDER_MADE_IN_DENMARK_PRICE,
                            'validation'    => [
                                'validate-number' => true
                            ],
                        ],
                    ],
                ],
            ]
        ];
    }
    private function getValueMadeInDenmarkStaticFieldConfig(): array
    {
        return [
            self::VALUE_KEY_MADE_IN_DENMARK_PRICE => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label'         => __('Made in Denmark Price'),
                            'componentType' => Field::NAME,
                            'component'     => 'Magento_Catalog/js/components/custom-options-component',
                            'formElement'   => Input::NAME,
                            'dataScope'     => self::VALUE_KEY_MADE_IN_DENMARK_PRICE,
                            'dataType'      => Number::NAME,
                            'addbefore'     => $this->getCurrencySymbol(),
                            'addbeforePool' => $this->productOptionsPrice->prefixesToOptionArray(),
                            'sortOrder'     => self::VALUE_SORT_ORDER_MADE_IN_DENMARK_PRICE,
                            'validation'    => [
                                'validate-number' => true
                            ],
                        ],
                    ],
                ],
            ]
        ];
    }

    /**
     * @throws NoSuchEntityException
     */
    protected function getCurrencySymbol(): string
    {
        return $this->storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();
    }
}