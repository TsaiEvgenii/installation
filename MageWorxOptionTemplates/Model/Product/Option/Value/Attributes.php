<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */
namespace BelVG\MageWorxOptionTemplates\Model\Product\Option\Value;

/**
 * Class Attributes
 * @package BelVG\MageWorxOptionTemplates\Model\Product\Option\Value
 */
class Attributes extends  \MageWorx\OptionBase\Model\Product\Option\Value\Attributes
{

    const DEPENDENCY_IDENTIFIER = "dependency";

    /**
     * @var array
     */
    private $data = [];

    /**
     * Attributes constructor.
     * @param array $data
     */
    public function __construct(
        \BelVG\MageWorxOptionTemplates\Model\Attribute\Dependency $dependency,
        $data = []
    ) {
        $data[self::DEPENDENCY_IDENTIFIER] = $dependency;
        $this->data = $data;
    }

    /**
     * @param null $key
     * @return mixed|null
     */
    public function getData($key = null)
    {
        if (!$key) {
            return $this->data;
        }

        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

}
