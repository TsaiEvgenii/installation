<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */
namespace BelVG\MageWorxOptionTemplates\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions;
use Magento\Ui\Component\Container;
use BelVG\MageWorxOptionTemplates\Helper\Config as MageWorxConfig;
use MageWorx\OptionBase\Ui\DataProvider\Product\Form\Modifier\ModifierInterface;


class HideOptions extends AbstractModifier implements ModifierInterface
{

    protected $mageworxConfig;
    /**
     * @var array|mixed
     */
    protected mixed $meta;

    public function __construct(
        MageWorxConfig $mageworxConfig
    )
    {
        $this->mageworxConfig = $mageworxConfig;
    }

    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $groupCustomOptionsName = CustomOptions::GROUP_CUSTOM_OPTIONS_NAME;

        $this->meta = $meta;

        // Add hide options block
        if (!$this->mageworxConfig->getConfig(MageWorxConfig::UPDATE_OPTIONS)) {
            $hideOptionsBlock = $this->getHideOptionsBlock();
            $this->meta[$groupCustomOptionsName]['children']['options']['children']['record']['children']['container_option']['children'] = array_replace_recursive(
                $this->meta[$groupCustomOptionsName]['children']['options']['children']['record']['children']['container_option']['children'],
                $hideOptionsBlock
            );
        }

        return $this->meta;
    }

    protected function getHideOptionsBlock()
    {

        $fields = [
            'hide_options_container' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Container::NAME,
                            'formElement' => Container::NAME,
                            'component' => 'Magento_Ui/js/form/components/group',
                            'breakLine' => false,
                            'showLabel' => false,
                            'additionalClasses' =>
                                'admin__field-control admin__control-grouped admin__field-group-columns hide-options-container',
                            'sortOrder' => 5,
                        ],
                    ],
                ]
            ],
        ];
        return $fields;
    }

    /**
     * Check is current modifier for the product only
     *
     * @return bool
     */
    public function isProductScopeOnly()
    {
        return false;
    }

}
