<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service;

use BelVG\MageWorxOptionServerSideRender\Model\Flag;

class CheckCacheStatusService
{
    private Flag $flag;

    /**
     * CheckCacheStatusService constructor.
     * @param Flag $flag
     */
    public function __construct(Flag $flag)
    {
        $this->flag = $flag;
    }
    /**
     * @param int $rule
     * @return bool
     */
    public function execute(int $ruleId, int $websiteId) :bool
    {
        $flag = $this->getFlag();
        $flagData = (array)$flag->getFlagData();
        if ($this->isActiveRule($ruleId, $flagData, $websiteId)) {
            return false;
        }
        return true;
    }
    
    protected function isActiveRule($ruleId, array $flagData, $websiteId)
    {
        return \array_key_exists($websiteId, $flagData) && $flagData[$websiteId] === $ruleId;
    }

    private function getFlag()
    {
        return $this->flag->loadSelf();
    }
}
