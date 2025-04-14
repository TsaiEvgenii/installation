<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionServerSideRender\Model\Service\ColorfulWindowType;

use BelVG\MageWorxOptionServerSideRender\Api\Data\ColorDescriptionInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\ColorfulWindowTypeProcessorInterface;
use Magento\Framework\Api\ObjectFactory;

abstract class AbstractColorProcessor implements ColorfulWindowTypeProcessorInterface
{
    const TYPE = 'default';
    private ?AbstractColorProcessor $next;

    /**
     * AbstractColorProcessor constructor.
     * @param  AbstractColorProcessor $next
     */
    public function __construct(ObjectFactory $objectFactory, $next = null )
    {
        /**
         * error with objectManager initialization, hot fix
         */
        if ($next === null) {
            return;
        }
        $this->next = is_object($next) === false ? $objectFactory->create($next, []) : $next;
    }

    public function getType(ColorDescriptionInterface $inColorDescription, ColorDescriptionInterface $outColorDescription) :string
    {
        if ($this->isSpecifiedType($inColorDescription, $outColorDescription)) {
            return static::TYPE;
        }
        return $this->next->getType($inColorDescription, $outColorDescription);
    }

    protected function isSpecifiedType($inColorDescription, $outColorDescription)
    {
        return true;
    }
}
