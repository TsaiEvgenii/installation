<?php
/**
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\LayoutCustomizer\Plugin\Magento\Sales\Model\Order\Item;

use BelVG\LayoutCustomizer\Helper\Data as LayoutCustomizerHelper;
use Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder;

class RemoveSectionSizesPlugin
{
    private $layoutDataHelper;

    public function __construct(
        LayoutCustomizerHelper $layoutDataHelper
    ) {
        $this->layoutDataHelper = $layoutDataHelper;
    }

    /**
     * Remove `{section_sizes}` from order_item in email
     *
     * @param DefaultOrder $subject
     * @param array $result
     * @return array
     */
    public function afterGetItemOptions(
        DefaultOrder $subject,
        array $result
    ) {
        $section_sizes = $this->layoutDataHelper->getSectionsSizesOption();

        foreach ($result as $option_key => $option) {
            if (isset($option['option_id'])) {
                $is_section_sizes = $this->layoutDataHelper->matchDbOptionWithConfig($option['option_id'], $section_sizes);

                if ($is_section_sizes) {
                    unset($result[$option_key]);
                }
            }

        }
        unset($option);

        return $result;
    }
}
