<?php

namespace BelVG\B2BCustomer\Plugin\Model\Customer;

use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\Customer\DataProviderWithDefaultAddresses as Subject;
use BelVG\B2BCustomer\Model\Config;
use Psr\Log\LoggerInterface;

class DataProviderWithDefaultAddresses
{
    private const LOG_PREFIX = '[BelVG_B2BCustomer::DataProviderWithDefaultAddresses]: ';

    /**
     * @param Config $config
     * @param CustomerRepository $customerRepository
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly Config $config,
        private readonly CustomerRepository $customerRepository,
        private readonly RequestInterface $request,
        private readonly LoggerInterface $logger,
    ) {

    }

    /**
     * @param Subject $subject
     * @param array $result
     * @return array
     */
    public function afterGetData(Subject $subject, array $result): array
    {
        try {
            $customerId = $this->request->get('id');
            if ($customerId) {

                $customer = $this->customerRepository->getById($customerId);
                $storeId = $customer->getStoreId() ? (int) $customer->getStoreId() : null;
                $groups = $this->config->getAllowedCustomerGroups($storeId);
                foreach ($result as $itemKey => $item) {
                    $result[$itemKey]['customer']['b2b_groups'] = $groups;
                }
            }
        } catch (\Exception $exception) {
            $this->logger->error(self::LOG_PREFIX . $exception->getMessage());
        }

        return $result;
    }

}
