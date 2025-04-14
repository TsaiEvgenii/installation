<?php


namespace BelVG\LayoutCustomizer\Block;


class Config extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $jsonHelper,
        \BelVG\LayoutCustomizer\Helper\Data $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    public function getJsonData()
    {
        $data = [
            'overall_width' => $this->helper->getOverallWidthOption(),
            'overall_height' => $this->helper->getOverallHeightOption(),
            'sections_sizes' => $this->helper->getSectionsSizesOption(),
        ];

        return $this->jsonHelper->serialize($data);
    }
}
