<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Spi;

use BelVG\MageWorxOptionServerSideRender\Api\Data\SelectedOptionInterface;

interface SelectedRequestOptionInterface
{
    /**
     * @param string[] $requestData
     * @return SelectedOptionInterface[]
     */
    public function get(array $requestData) :iterable;
}
