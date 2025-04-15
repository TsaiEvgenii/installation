<?php

namespace BelVG\MageWorxOptionTemplates\Model;

use Magento\Framework\MessageQueue\MergerInterface;

/**
 * Class Merger
 * @package BelVG\MageWorxOptionTemplates\Model
 */
class Merger implements MergerInterface
{
    /**
     * @param array $messages
     * @return array|\Magento\Framework\MessageQueue\MergedMessageInterface[]|object[]
     */
    public function merge(array $messages)
    {
        return $messages;
    }
}