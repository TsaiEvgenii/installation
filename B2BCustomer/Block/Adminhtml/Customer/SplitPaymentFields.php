<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Block\Adminhtml\Customer;

use BelVG\B2BCustomer\Model\Config;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class SplitPaymentFields extends \Magento\Backend\Block\Template
{

    /**
     * @var string
     */
    protected $_template = 'split_payment_fields.phtml';

    /**
     * @var \BelVG\B2BCustomer\Model\Config
     */
    protected $config;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var mixed|null
     */
    protected $customer;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \BelVG\B2BCustomer\Model\Config $config
     * @param DataPersistorInterface $dataPersistor
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \BelVG\B2BCustomer\Model\Config         $config,
        DataPersistorInterface                  $dataPersistor,
        array                                   $data = [],
        ?JsonHelper                             $jsonHelper = null,
        ?DirectoryHelper                        $directoryHelper = null
    )
    {
        $this->config = $config;
        $this->dataPersistor = $dataPersistor;
        $this->customer = $this->getCustomer();
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
    }

    /**
     * @return mixed|null
     */
    public function getCustomer()
    {
        $customerData = $this->dataPersistor->get('customer');
        return $customerData['account'] ?? null;
    }

    /**
     * @return mixed|true
     */
    public function isEnabled()
    {
        if ($this->customer) {
            return $this->config->getIsSplitEnabled((int)($this->customer['store_id'] ?? null));
        }
        return true;
    }

    /**
     * @param int $index
     * @return array|string|string[]
     */
    public function getPaymentStatus(int $index)
    {
        if ($this->customer) {
            $status = $this->config->getPaymentStatus($index, (int)($this->customer['store_id'] ?? null));
            if ($status) {
                return str_replace('_', ' ', ucfirst($status));
            }
        }
        return '';
    }


    /**
     * @param $key
     * @return mixed|string
     */
    public function getValue($key)
    {
        if ($this->customer && isset($this->customer[$key])) {
            return $this->customer[$key];
        }
        return '';
    }

    /**
     * @return int
     */
    public function getPaymentsCount()
    {
        return Config::PAYMENTS_COUNT;
    }


}
