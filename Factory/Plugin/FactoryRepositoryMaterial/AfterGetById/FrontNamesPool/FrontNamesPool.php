<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Plugin\FactoryRepositoryMaterial\AfterGetById\FrontNamesPool;

class FrontNamesPool
{
    /**
     * @var string[]
     */
    protected array $frontNames;

    /**
     * @param string[] $frontNames
     */
    public function __construct(array $frontNames = [])
    {
        $this->frontNames = $frontNames;
    }

    /**
     * @return string[]
     */
    public function getFrontNames(): array
    {
        return $this->frontNames;
    }
}
