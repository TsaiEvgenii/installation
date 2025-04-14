<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2024
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Block\Product\View;


use BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedOptions;
use Magento\Catalog\Block\Product\View\Options as OptionsParent;
use Magento\Framework\App\ObjectManager;

class Options extends OptionsParent
{
    private GetSelectedOptions $selectedOptions;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
                                \Magento\Framework\Pricing\Helper\Data $pricingHelper,
                                \Magento\Catalog\Helper\Data $catalogData,
                                \Magento\Framework\Json\EncoderInterface $jsonEncoder,
                                \Magento\Catalog\Model\Product\Option $option,
                                \Magento\Framework\Registry $registry,
                                \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
                                array $data = [])
    {
        parent::__construct($context, $pricingHelper, $catalogData, $jsonEncoder, $option, $registry, $arrayUtils, $data);
        $this->selectedOptions = ObjectManager::getInstance()->get(GetSelectedOptions::class);
    }

    protected function getCacheLifetime()
    {
        return 3600;
    }


    public function getCacheKeyInfo()
    {
        $data = parent::getCacheKeyInfo();
        foreach ($this->selectedOptions as $selectedOption) {
            $data[$selectedOption->getOptionId()] = $selectedOption->getValue();
        }
        return $data;
    }
}