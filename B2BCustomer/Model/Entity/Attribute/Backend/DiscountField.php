<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Model\Entity\Attribute\Backend;


use BelVG\B2BCustomer\Model\Config;
use Magento\Framework\Exception\LocalizedException;

class DiscountField extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    )
    {
        $this->config = $config;
    }

    /**
     * @param $object
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate($object)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        if ($object->hasData($attributeCode)) {
            $discount = (int)$object->getData($attributeCode);
            $maxValue = $this->config->getDiscountMaxValue();
            if ($maxValue && ($discount < 0 || $discount > $maxValue)) {
                throw new LocalizedException(
                    __('B2B discount should be lower then %1%', $maxValue)
                );
            }
        }

        return parent::validate($object);
    }

}
