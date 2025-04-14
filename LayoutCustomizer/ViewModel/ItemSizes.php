<?php
declare(strict_types=1);

namespace BelVG\LayoutCustomizer\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer;
use BelVG\LayoutCustomizer\Helper\Data as LayoutHelper;

class ItemSizes implements ArgumentInterface
{
    const SIZES_POSTFIX = " cm";
    const SIZES_DELIMITER = " x ";
    private $width = [];
    private $height = [];
    public function __construct(
        private readonly DefaultRenderer $defaultRenderer,
        private readonly LayoutHelper $layoutHelper
    ) {}

    private function getItemSizes($itemId, $options)
    {
        $overall_width = $this->layoutHelper->getOverallWidthOption();
        $overall_height = $this->layoutHelper->getOverallHeightOption();
        if ($options) {
            foreach ($options as $option) {
                $formatedOptionValue = $this->defaultRenderer->getFormatedOptionValue($option);
                $isWidth = isset($option['option_id']) ?
                    $this->layoutHelper->matchDbOptionWithConfig($option['option_id'], $overall_width)
                    : false;
                $isHeight = isset($option['option_id']) ?
                    $this->layoutHelper->matchDbOptionWithConfig($option['option_id'], $overall_height)
                    : false;

                if (($isWidth || $isHeight) && isset($option['option_type_id'])) {
                    $formatedOptionValue = [
                        'value' => $option['option_type_id']
                    ];
                }

                if ($isWidth) {
                    $this->width[$itemId] = isset($formatedOptionValue['full_view']) ? $formatedOptionValue['full_view'] : $formatedOptionValue['value'];
                }
                if ($isHeight) {
                    $this->height[$itemId] = isset($formatedOptionValue['full_view']) ? $formatedOptionValue['full_view'] : $formatedOptionValue['value'];
                }
            }
        }
    }

    public function getItemWidth($itemId, $options)
    {
        if(!isset($this->width[$itemId]) && $options) {
            $this->getItemSizes($itemId, $options);
        }
        return isset($this->width[$itemId]) ? $this->width[$itemId] : false;
    }

    public function getItemHeight($itemId, $options)
    {
        if(!isset($this->height[$itemId]) && $options) {
            $this->getItemSizes($itemId, $options);
        }
        return isset($this->height[$itemId]) ? $this->height[$itemId] : false;
    }

    public function getItemSizesString($itemId, $itemOptions, $eng = false): ?string
    {
        $itemWidth = $this->getItemWidth($itemId, $itemOptions);
        $itemHeight = $this->getItemHeight($itemId, $itemOptions);
        if($itemWidth && $itemHeight) {
            $postfix = $eng ? self::SIZES_POSTFIX : __(self::SIZES_POSTFIX);
            return $itemWidth . self::SIZES_DELIMITER . $itemHeight . $postfix;
        }
        return null;
    }
}
