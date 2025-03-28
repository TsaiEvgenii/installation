<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Model\Entity\Attribute\Source;


class CustomerGroup extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected $customerGroup;

    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup
    ) {
        $this->customerGroup = $customerGroup;
    }

    protected function getGroups()
    {
        return $this->customerGroup->toOptionArray();
    }

    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'value' => \BelVG\MasterAccount\Helper\Data::VALUE_EMPTY,
                    'label' => __(
                        \BelVG\MasterAccount\Helper\Data::LABEL_EMPTY
                    )
                )
            );

            foreach ($this->getGroups() as $group) {
                $this->_options[] = array(
                    'value' => $group['value'],
                    'label' => $group['label'],
                );
            }
        }

        return $this->_options;
    }
}
