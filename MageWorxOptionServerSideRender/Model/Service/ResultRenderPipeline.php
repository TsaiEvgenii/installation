<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service;

use BelVG\MageWorxOptionServerSideRender\Api\RenderBlockInterface;
use BelVG\MageWorxOptionServerSideRender\Api\ResultRenderPipelineInterface;
use Magento\Framework\View\LayoutInterface;

class ResultRenderPipeline implements ResultRenderPipelineInterface
{
    /**
     * @var CreateBlockServiceInterface
     */
    private CreateBlockServiceInterface $createBlockService;

    /**
     * ResultRenderPipeline constructor.
     * @param CreateBlockServiceInterface $createBlockService
     */
    public function __construct(CreateBlockServiceInterface $createBlockService)
    {
        $this->createBlockService = $createBlockService;
    }

    public function process(string $result, $option): string
    {
        /**
         * @var RenderBlockInterface $render
         */
        $render = $this->getRender($option);
        $result = $render->process($result);
        return $result;
    }

    private function getRender($option) :RenderBlockInterface
    {
        return $this->createBlockService->createBlock($option);
    }
}
