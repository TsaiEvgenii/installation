<?php
/**
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\LayoutCustomizer\Plugin\Magento\Quote\Data\Totals;

use \BelVG\LayoutCustomizer\Helper\Layout\SizeVariables as SizeVariablesHelper;

class AddLayoutDataToItem
{
    public function __construct(
        private readonly \BelVG\LayoutCustomizer\Helper\Data $layoutDataHelper,
        private readonly \Magento\Quote\Model\ResourceModel\Quote\Item\Option\CollectionFactory $optionsCollectionFactory,
        private readonly SizeVariablesHelper $sizeVariablesHelper
    ) {
    }

    protected function getOptionId(array $option) {
        return str_replace('option_', '', $option['code']);
    }

    protected function getLayoutHeight(
        \Magento\Quote\Api\Data\TotalsItemInterface $item,
        array $option
    ) {
        $overall_height = $this->layoutDataHelper->getOverallHeightOption();
        $is_height = $this->layoutDataHelper->matchDbOptionWithConfig(
            $this->getOptionId($option),
            $option['mageworx_optiontemplates_group_option_type_id'],
            $overall_height
        );

        if ($is_height) {
            $extensionAttributes = $item->getExtensionAttributes();
            $extensionAttributes->setLayoutHeight($option['value']);
            $item->setExtensionAttributes($extensionAttributes);
        }
    }

    protected function getLayoutWidth(
        \Magento\Quote\Api\Data\TotalsItemInterface $item,
        array $option
    ) {
        $overall_width = $this->layoutDataHelper->getOverallWidthOption();
        $is_width = $this->layoutDataHelper->matchDbOptionWithConfig(
            $this->getOptionId($option),
            $option['mageworx_optiontemplates_group_option_type_id'],
            $overall_width
        );

        if ($is_width) {
            $extensionAttributes = $item->getExtensionAttributes();
            $extensionAttributes->setLayoutWidth($option['value']);
            $item->setExtensionAttributes($extensionAttributes);
        }
    }

    protected function getLayoutSectionSizes(
        \Magento\Quote\Api\Data\TotalsItemInterface $item,
        array $option
    ) {
        $section_sizes = $this->layoutDataHelper->getSectionsSizesOption();
        $is_section_sizes = $this->layoutDataHelper->matchDbOptionWithConfig($this->getOptionId($option), $section_sizes);

        if ($is_section_sizes) {
            $extensionAttributes = $item->getExtensionAttributes();
            $extensionAttributes->setLayoutSectionSizes($option['value']);
            $item->setExtensionAttributes($extensionAttributes);
        }
    }

    public function afterSetItems(
        \Magento\Quote\Api\Data\TotalsInterface $subject,
        $result,
        array $items = null
    ) {
        foreach ($result->getItems() as $item) {
            if (is_array($item)) {
                break; //already applied
            }

            $options = $this->optionsCollectionFactory->create();
            $options->addItemFilter($item->getItemId());
            foreach ($options as $option) {
                foreach ($this->sizeVariablesHelper->getLayoutVariablesPool() as $var) {
                    $method_name = 'get' . $this->sizeVariablesHelper->camelize($var);
                    $this->{$method_name}($item, $option->toArray());
                }
                unset($var);
            }
            unset($option);
        }


        return $result;
    }

}
