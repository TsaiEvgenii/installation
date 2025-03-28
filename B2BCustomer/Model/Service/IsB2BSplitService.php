<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\B2BCustomer\Model\Service;


use BelVG\B2BCustomer\Model\Config;
use Magento\Customer\Model\Customer;

class IsB2BSplitService
{

    /**
     * @var Config
     */
    protected $config;

    protected $customerCheck;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config,
        CustomerCheck $customerCheck
    )
    {
        $this->customerCheck = $customerCheck;
        $this->config = $config;
    }

    /**
     * @param $customerGroupId
     * @return bool
     */
    public function isAllowed($customerGroupId, $storeId): bool
    {
        if ($customerGroupId && $this->customerCheck->isB2BCustomer($customerGroupId, $storeId) && $this->config->getIsSplitEnabled((int)$storeId)) {
            return true;
        }
        return false;
    }
}
