<?php
/**
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\LayoutCustomizer\Block\Product\View;


class QtyPriceMultiplier extends \Magento\Framework\View\Element\Template
{
    protected $_localeFormat;

    protected $_jsonEncoder;

    /**
     * Timer constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_localeFormat = $localeFormat;

        parent::__construct($context, $data);
    }

    public function getJsonConfig()
    {
        $config = [
            'priceFormat' => $this->_localeFormat->getPriceFormat(),
        ];
        if ($configurePage = $this->getData('configure_page')) {
            $config['configurePage'] = $configurePage;
        }

        return $this->_jsonEncoder->encode($config);
    }
}
