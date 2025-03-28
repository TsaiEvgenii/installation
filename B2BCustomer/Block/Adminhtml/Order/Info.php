<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Block\Adminhtml\Order;

use Magento\Backend\Block\Template;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use BelVG\B2BCustomer\Model\Config;

class Info extends Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var
     */
    protected $currentCustomer;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $userFactory;

    /**
     * @param Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param Config $config
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        Template\Context                                  $context,
        \Magento\Framework\Registry                       $registry,
        Config                                            $config,
        \Magento\User\Model\UserFactory                   $userFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        array                                             $data = [],
        ?JsonHelper                                       $jsonHelper = null,
        ?DirectoryHelper                                  $directoryHelper = null
    )
    {
        $this->config = $config;
        $this->customerRepository = $customerRepository;
        $this->userFactory = $userFactory;
        $this->registry = $registry;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
    }

    /**
     * @return mixed|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOrder()
    {
        if ($this->registry->registry('current_order')) {
            return $this->registry->registry('current_order');
        }
        if ($this->registry->registry('order')) {
            return $this->registry->registry('order');
        }
        throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t get the order instance right now.'));

    }


    /**
     * @return false|\Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomer()
    {
        if (!$this->currentCustomer) {
            try {
                $order = $this->getOrder();
                $customerId = $order->getCustomerId();
                if ($customerId) {
                    $this->currentCustomer = $this->customerRepository->getById($customerId);
                } else {
                    return false;
                }
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return $this->currentCustomer;
    }

    /**
     * @param $userId
     * @return mixed|string|null
     */
    public function getUserName($userId)
    {
        /** @phpstan-ignore-next-line */
        return $this->userFactory->create()->load($userId)->getUserName();
    }

}
