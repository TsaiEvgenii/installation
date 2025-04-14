<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Plugin\Magento\Catalog\Block\Product\View\Options\Type;

use BelVG\MageWorxOptionServerSideRender\Api\ResultRenderPipelineInterface;

class SelectOverridePlugin
{
    private ResultRenderPipelineInterface $resultRenderPipeline;

    /**
     * SelectOverridePlugin constructor.
     * @param ResultRenderPipelineInterface $resultRenderPipeline
     */
    public function __construct(ResultRenderPipelineInterface $resultRenderPipeline)
    {
        $this->resultRenderPipeline = $resultRenderPipeline;
    }
    public function aroundGetValuesHtml($subject, $proceed)
    {
        $result = $proceed();
        $option = $subject->getOption();
        return $this->resultRenderPipeline->process($result, $option);
    }
}
