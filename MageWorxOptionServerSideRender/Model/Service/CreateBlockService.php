<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionServerSideRender\Model\Service;

use BelVG\MageWorxOptionServerSideRender\Api\RenderBlockInterface;
use BelVG\MageWorxOptionServerSideRender\Api\RenderSpecificationInterface;
use BelVG\MageWorxOptionServerSideRender\Api\SpecificationInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Framework\View\LayoutInterface;

class CreateBlockService implements CreateBlockServiceInterface
{
    /**
     * @var LayoutInterface
     */
    private LayoutInterface $layout;
    /**
     * @var RenderSpecificationInterface[]
     */
    private array $specifications;
    /**
     * @var RenderSpecificationInterface
     */
    private RenderSpecificationInterface $defaultSpecification;

    /**
     * CreateBlockService constructor.
     * @param LayoutInterface $layout
     * @param RenderSpecificationInterface[] $specifications
     * @param RenderSpecificationInterface $defaultSpecification
     */
    public function __construct(
        LayoutInterface $layout,
        RenderSpecificationInterface $defaultSpecification,
        array $specifications = []
    ) {
        $this->layout = $layout;
        $this->specifications = $specifications;
        $this->defaultSpecification = $defaultSpecification;
    }
    /**
     * @param ProductCustomOptionInterface $option
     * @return RenderBlockInterface
     */
    public function createBlock(ProductCustomOptionInterface $option): RenderBlockInterface
    {
        foreach ($this->specifications as $specification) {
            if ($specification->isSpecifiedBy($option)) {
                /**
                 * @var \Magento\Framework\View\Element\BlockInterface $block
                 */
                return $this->buildBlock($option, $specification);
            }
        }
        return $this->buildBlock($option, $this->defaultSpecification);
    }

    private function buildBlock($option, $specification)
    {
        $block = $this->layout->createBlock($specification->getBlock(), '', ['data'=>['option'=>$option]]);
        $block->setTemplate($specification->getTemplate());
        $specification->setAdditionalActions($block);
        return $block;
    }
}
