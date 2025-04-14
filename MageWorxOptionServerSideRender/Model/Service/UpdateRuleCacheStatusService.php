<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionServerSideRender\Model\Service;

use BelVG\MageWorxOptionServerSideRender\Model\Flag;
use Magento\SalesRule\Api\Data\RuleInterface;

class UpdateRuleCacheStatusService
{
    private Flag $flag;

    /**
     * UpdateRuleCacheStatusService constructor.
     * @param Flag $flag
     */
    public function __construct(Flag $flag)
    {
        $this->flag = $flag;
    }
    public function execute(int $ruleId, int $websiteId)
    {
        $loadedFlag = $this->flag->loadSelf();
        $flagData = (array)$loadedFlag->getFlagData();
        $flagData[$websiteId] = $ruleId;
        $loadedFlag->setFlagData($flagData);
        /** @phpstan-ignore-next-line */
        $loadedFlag->save();
    }
}
