<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Block\Option\Type;

use BelVG\MageWorxOptionServerSideRender\Api\Data\SelectedOptionInterface;
use BelVG\MageWorxOptionServerSideRender\Model\SelectedOptionValue;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedOptions;
use BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedSpecialColor;
use BelVG\MageWorxOptionServerSideRender\Model\Service\SelectedOptionProcessor;
use BelVG\MageWorxSpecialColor\Api\Data\SpecialColorRalCodeInterface;
use BelVG\MageWorxSpecialColor\Model\Data\SpecialColorRalCode;
use BelVG\MageWorxSpecialColor\Model\Service\SpecialColor;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\OptionFeatures\Helper\Data;

class SpecialColorOptionWrapperBlock extends Template implements ArgumentInterface
{
    use SelectedOptionProcessor;
    /**
     * @var SpecialColor
     */
    private SpecialColor $specialColor;
    /**
     * @var GetSelectedOptions
     */
    private GetSelectedOptions $selectedOptions;
    /**
     * @var Data
     */
    private Data $mageWorxHelper;
    /**
     * @var GetSelectedSpecialColor
     */
    private GetSelectedSpecialColor $selectedSpecialColor;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * SpecialColorOptionWrapperBlock constructor.
     * @param Template\Context $context
     * @param GetSelectedSpecialColor $selectedSpecialColor
     * @param StoreManagerInterface $storeManager
     * @param Data $mageWorxHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        GetSelectedSpecialColor $selectedSpecialColor,
        Data $mageWorxHelper,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->mageWorxHelper = $mageWorxHelper;
        $this->selectedSpecialColor = $selectedSpecialColor;
        $this->storeManager = $storeManager;
    }


    public function getSelectedSpecialColor($option) :SpecialColorRalCodeInterface
    {
        return $this->selectedSpecialColor->get($option);
    }


    public function getColorName(SpecialColorRalCodeInterface $specialColorRalCode) :string
    {
        return \str_replace('ral_', '', $specialColorRalCode->getIdentifier());
    }

    public function getImageUrl(SpecialColorRalCode $specialColorRalCode) :string
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $specialColorRalCode->getImg();
    }
}
