<?php
namespace BelVG\Factory\Ui\DataProvider\Factory\Form\Modifier;

use BelVG\Factory\Api\FactoryMaterialRepositoryInterface;
use BelVG\Factory\Model\Config\Source\FactoryMaterialDeliveryTypes;
use BelVG\Factory\Model\Config\Source\FactoryMaterialDeliveryTypesOptions;
use BelVG\Factory\Model\Config\Source\AffectingDeliveryTimeParameters;
use BelVG\LayoutMaterial\Model\Config\Source\Material as MaterialSource;
use Magento\Directory\Model\Config\Source\Country as CountrySource;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Registry;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\ActionDelete;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Store\Model\StoreManagerInterface;

class Materials extends AbstractModifier
{
    protected $storeManager;
    protected $factoryMaterialRepo;
    protected $materialSource;
    protected $countrySource;
    protected $colorSource;
    protected $searchCriteriaBuilder;
    protected $sortOrderBuilder;
    protected $dataObjectConverter;

    /**
     * @var FactoryMaterialDeliveryTypes
     */
    protected FactoryMaterialDeliveryTypes $factoryMaterialDeliveryTypes;

    /**
     * @var FactoryMaterialDeliveryTypesOptions
     */
    protected FactoryMaterialDeliveryTypesOptions $factoryMaterialDeliveryTypesOptions;

    /**
     * @var AffectingDeliveryTimeParameters
     */
    protected AffectingDeliveryTimeParameters $affectingDeliveryTimeParameters;

    public function __construct(
        StoreManagerInterface $storeManager,
        Registry $registry,
        FactoryMaterialRepositoryInterface $factoryMaterialRepo,
        MaterialSource $materialSource,
        CountrySource $countrySource,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        ExtensibleDataObjectConverter $dataObjectConverter,
        FactoryMaterialDeliveryTypes $factoryMaterialDeliveryTypes,
        FactoryMaterialDeliveryTypesOptions $factoryMaterialDeliveryTypesOptions,
        AffectingDeliveryTimeParameters $affectingDeliveryTimeParameters
    ) {
        parent::__construct($registry);
        $this->storeManager = $storeManager;
        $this->factoryMaterialRepo = $factoryMaterialRepo;
        $this->materialSource = $materialSource;
        $this->countrySource = $countrySource;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->dataObjectConverter = $dataObjectConverter;
        $this->factoryMaterialDeliveryTypes = $factoryMaterialDeliveryTypes;
        $this->factoryMaterialDeliveryTypesOptions = $factoryMaterialDeliveryTypesOptions;
        $this->affectingDeliveryTimeParameters = $affectingDeliveryTimeParameters;
    }


    public function modifyMeta(array $meta)
    {
        $this->addMaterialFieldset($meta);

        return $meta;
    }

    protected function addMaterialFieldset(array &$meta)
    {
        $meta['materials'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Fieldset::NAME,
                        'label' => __('Materials')
                    ]]],
            'children' => [
                'materials' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType'       => DynamicRows::NAME,
                                'component'           => 'BelVG_Factory/js/material-rows-unique',
                                'label'               => __('Materials'),
                                'renderDefaultRecord' => false,
                                'additionalClasses'   => 'admin__field-wide',
                                'recordTemplate'      => 'record']]],
                    'children' => [
                        'record' => $this->getMaterialRecordLayout()
                    ]]
            ]];
    }

    protected function getMaterialRecordLayout()
    {
        $materialLayout = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Container::NAME,
                        'component'     => 'Magento_Ui/js/dynamic-rows/record',
                        'dataScope'     => '',
                        'isTemplate'    => true,
                        'is_collection' => true
                    ]]],
            'children' => [
                'material-container' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Fieldset::NAME,
                                'additionalClasses' => 'factory-material-field',
                                'collapsible'   => true,
                                'label'         => null,
                                'opened'        => true,
                                'sortOrder'     => 10
                            ]]],
                    'children' => [
                        'material_id' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Field::NAME,
                                        'component'     => 'BelVG_Factory/js/form/element/select-with-store-context-logic',
                                        'elementTmpl'     => 'BelVG_Factory/form/element/select-with-desc',
                                        'label'         => __('Material'),
                                        'description'   => __('Determine what materials can be used with Factory. <br> Global materials can\'t be configured at the store level.'),
                                        'formElement'   => Select::NAME,
                                        'dataType'      => Text::NAME,
                                        'dataScope'     => 'material_id',
                                        'options'       => $this->materialSource->getAllOptions(),
                                        'required'      => true,
                                        'validation'    => ['required-entry' => true],
                                        'sortOrder'     => 10,
                                    ]]]],
                        'priority' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Field::NAME,
                                        'component'     => 'BelVG_Factory/js/form/element/abstract-with-store-context-logic',
                                        'elementTmpl'   => 'BelVG_Factory/form/element/input-with-desc',
                                        'label'         => __('Priority'),
                                        'description'   => __('Works only for the "order split" feature. <br> In case there are several factories that can produce the order they will be sorted based on priority (100 is more important then 0)'),
                                        'dataType'      => Text::NAME,
                                        'formElement'   => Input::NAME,
                                        'dataScope'     => 'priority',
                                        'sortOrder'     => 15,
                                    ]]]],
                        'delivery_rules' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType'       => DynamicRows::NAME,
                                        'component'           => 'BelVG_Factory/js/delivery-rule-rows-unique',
                                        'label'               => __('Delivery'),
                                        'renderDefaultRecord' => false,
                                        'recordTemplate'      => 'record',
                                        'dataScope'           => '',
                                        'sortOrder'           => 20
                                    ]]],
                            'children' => [
                                'record' => $this->getDeliveryRuleRecordLayout()
                            ]]
                    ]],
                'actionDelete' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => ActionDelete::NAME,
                                'component' => 'BelVG_Factory/js/dynamic-rows/action-delete-with-store-context-logic',
                                'dataType'      => Text::NAME,
                                'label'         => '',
                                'sortOrder'     => 30
                            ]]]]
            ]];

        if ($this->storeManager->getStore()->getId() == 0) {
            unset($materialLayout['children']['material-container']['children']['delivery_rules']);
        }

        return $materialLayout;
    }

    protected function getDeliveryRuleRecordLayout()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType'    => Container::NAME,
                        'component'        => 'Magento_Ui/js/dynamic-rows/record',
                        'positionProvider' => 'sort_order',
                        'isTemplate'       => true,
                        'is_collection'    => true
                    ]]],
            'children' => [
                'types' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'component' => "Magento_Ui/js/form/element/select",
                                'filterOptions' => false,
                                'showCheckbox' => false,
                                'multiple' => false,
                                'disableLabel' => true,
                                'label' => __('Types'),
                                'formElement' => Select::NAME,
                                'dataType' => Text::NAME,
                                'dataScope' => 'types',
                                'options' => $this->factoryMaterialDeliveryTypes->toOptionArray(),
                                'required' => true,
                                'validation' => ['required-entry' => true],
                                'sortOrder' => 9,
                            ]
                        ]
                    ]
                ],
                'category_id' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType'    => Field::NAME,
                                'component'        => 'BelVG_Factory/js/form/element/component-type-select',
                                'label'            => __('Category/Templates'),
                                'formElement'      => Select::NAME,
                                'dataType'         => Text::NAME,
                                'showCheckbox'     => false,
                                'multiple'         => false,
                                'filterOptions'    => true,
                                'chipsEnabled'     => true,
                                'disableLabel'     => true,
                                'levelsVisibility' => '1',
                                'elementTmpl'      => 'ui/grid/filters/elements/ui-select',
                                'options'          => $this->factoryMaterialDeliveryTypesOptions->toOptionArray(),
                                'value'            => '0',
                                'filterBy'         => [
                                    'target' => '${ $.provider }:${ $.parentScope }.types',
                                    '__disableTmpl' => ['target' => false],
                                    'field' => 'type',
                                ],
                                'listens' => [
                                    'index=create_category:responseData' => 'setParsed',
                                    'newOption' => 'toggleOptionSelected'
                                ],
                                'config' => [
                                    'dataScope' => 'category_id',
                                    'sortOrder' => 20
                                ],
                                'sortOrder' => 10,
                            ]]]],
                'colors' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'component' => 'BelVG_Factory/js/form/element/component-options-select',
                                'label' => __('Colors/Template Options'),
                                'formElement' => Select::NAME,
                                'dataType' => Text::NAME,
                                'showCheckbox' => false,
                                'multiple' => false,
                                'filterOptions' => true,
                                'chipsEnabled' => true,
                                'disableLabel' => true,
                                'levelsVisibility' => '1',
                                'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                                'options' => $this->affectingDeliveryTimeParameters->toOptionArray(),
                                'sortOrder' => 20,
                                'filterBy' => [
                                    'target' => '${ $.provider }:${ $.parentScope }.category_id',
                                    '__disableTmpl' => ['target' => false],
                                    'field' => 'option_group_id',
                                    'additionalField' => 'option_type',
                                ],
                                'config' => [
                                    'dataScope' => 'colors'
                                ],
                            ]]]],
                'delivery_time' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'label'         => __('Delivery time (weeks)'),
                                'formElement'   => Input::NAME,
                                'dataType'      => Text::NAME,
                                'dataScope'     => 'delivery_time',
                                'sortOrder'     => 30
                            ]]]],
                'sort_order' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'label'         => __('Sort Order'),
                                'formElement'   => Input::NAME,
                                'dataType'      => Text::NAME,
                                'dataScope'     => 'sort_order',
                                'visible'       => false,
                                'sortOrder'     => 40]]]],
                'actionDelete' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => ActionDelete::NAME,
                                'dataType'      => Text::NAME,
                                'label'         => '',
                                'sortOrder'     => 100
                            ]]]],
            ]];
    }
}
