<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\B2BCustomer\Model\Service;

use BelVG\B2BCustomer\Model\Config;

class CustomerCheck
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config,
    ) {
        $this->config = $config;
    }

    /**
     * @param $customerGroupId
     * @param $storeId
     * @return bool
     */
    public function isB2BCustomer($customerGroupId, $storeId): bool
    {
        $allowedGroups = explode(',', $this->config->getAllowedCustomerGroups((int)$storeId) ?? '');
        return $allowedGroups && $customerGroupId && in_array($customerGroupId, $allowedGroups);
    }
}
