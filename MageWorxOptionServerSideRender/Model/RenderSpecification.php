<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model;

use BelVG\MageWorxOptionServerSideRender\Api\AdditionalRenderActionInterface;
use BelVG\MageWorxOptionServerSideRender\Api\RenderBlockInterface;
use BelVG\MageWorxOptionServerSideRender\Api\RenderSpecificationInterface;
use BelVG\MageWorxOptionServerSideRender\Api\SpecificationInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionInterface;

class RenderSpecification implements RenderSpecificationInterface
{
    /**
     * @var SpecificationInterface
     */
    private SpecificationInterface $specification;

    private string $blockName;
    private string $template;
    /**
     * @var AdditionalRenderActionInterface
     */
    private AdditionalRenderActionInterface $additionalRenderAction;

    /**
     * RenderSpecification constructor.
     * @param SpecificationInterface $specification
     * @param AdditionalRenderActionInterface $additionalRenderAction
     * @param string $blockName
     * @param string $template
     */
    public function __construct(
        SpecificationInterface $specification,
        AdditionalRenderActionInterface $additionalRenderAction,
        string $blockName,
        string $template
    ) {
        $this->specification = $specification;
        $this->blockName = $blockName;
        $this->template = $template;
        $this->additionalRenderAction = $additionalRenderAction;
    }

    public function isSpecifiedBy(ProductCustomOptionInterface $option): bool
    {
        return $this->specification->isSpecifiedBy($option);
    }

    public function getBlock(): string
    {
        return $this->blockName;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setAdditionalActions(RenderBlockInterface $block):void
    {
        $this->additionalRenderAction->process($block);
    }
}
