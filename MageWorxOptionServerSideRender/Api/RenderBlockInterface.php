<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Api;

interface RenderBlockInterface
{
    /**
     * @param string $result
     * @return string
     */
    public function process(string $result) :string;

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate($template);
}
