<?php
/**
 * @package Vinduesgrossisten.
 * @author Tsai Eugene <tsai.evgenii@gmail.com>
 * Copyright (c) 2025.
 */
declare(strict_types=1);

namespace BelVG\OrderUpgrader\Block\Adminhtml\Form\Field;

use BelVG\LayoutMaterial\Api\LayoutMaterialRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Context;

class MaterialRenderer extends Select
{
    /**
     * @var LayoutMaterialRepositoryInterface
     */
    private $layoutMaterialRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var array
     */
    private $materialsOptions;

    /**
     * @param Context $context
     * @param LayoutMaterialRepositoryInterface $layoutMaterialRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        LayoutMaterialRepositoryInterface $layoutMaterialRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        $this->layoutMaterialRepository = $layoutMaterialRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context, $data);
    }

    /**
     * Set input name
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set input id
     *
     * @param string $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getMaterialOptions());
        }
        return parent::_toHtml();
    }

    /**
     * Get materials as options array
     *
     * @return array
     */
    private function getMaterialOptions(): array
    {
        if (!$this->materialsOptions) {
            $this->materialsOptions = [];

            $searchCriteria = $this->searchCriteriaBuilder->create();
            $materials = $this->layoutMaterialRepository->getList($searchCriteria);

            foreach ($materials->getItems() as $material) {
                $this->materialsOptions[] = [
                    'value' => $material->getId(),
                    'label' => $material->getName()
                ];
            }
        }

        return $this->materialsOptions;
    }

    /**
     * Generate hash for the material option
     *
     * @param string $optionValue
     * @return string
     */
    public function calcOptionHash($optionValue): string
    {
        return sprintf('%u', crc32($optionValue));
    }
}