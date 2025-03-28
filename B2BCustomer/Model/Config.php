<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\B2BCustomer\Model;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    public const B2B_PAYMENT_METHODS = 'b2b_customer/general/show_for_payment_methods';
    public const B2B_ALLOWED_CUSTOMER_GROUPS = 'b2b_customer/general/allowed_groups';
    public const B2B_ALLOWED_CONTACT_EMAIL = 'b2b_customer/general/email';
    public const B2B_INVOICE_PAYMENT_DEADLINE = 'b2b_customer/general/payment_deadline_invoice';
    public const B2B_QUOTE_PAYMENT_DEADLINE = 'b2b_customer/general/payment_deadline_quote';
    public const B2B_DISCOUNT_MAX_VALUE = 'b2b_customer/general/discount_max_value';
    public const B2B_DISCOUNT_MAX_VALUE_CART = 'b2b_customer/general/discount_max_value_cart';
    public const B2B_SHIPPING_DISCOUNT_ENABLED = 'b2b_customer/general/shipping_discount_enabled';
    public const B2B_SPLIT_IS_ENABLED = 'b2b_customer/split_payment/enabled';
    public const B2B_SPLIT_INVOICE_TEXT = 'b2b_customer/split_payment/invoice_text';
    public const B2B_SPLIT_PAYMENT_STATUS = 'b2b_customer/split_payment/payment_status_';
    public const B2B_SPLIT_EMAIL_TEMPLATE = 'b2b_customer/split_payment/email_template_';
    public const B2B_SPLIT_EMAIL_ADDRESSES = 'b2b_customer/split_payment/email';
    public const B2B_SPLIT_DISABLE_STATUS_EMAIL_SEND = 'b2b_customer/split_payment/disable_status_email_send';

    public const B2B_SPLIT_PAYMENT_ADDITIONAL_INFO_KEY  = 'b2b_partial_payment_data';

    public const PAYMENTS_COUNT = 3;


    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param int|null $storeId
     * @return mixed
     */
    public function getPaymentMethods(?int $storeId = null)
    {
        return $this->scopeConfig->getValue(self::B2B_PAYMENT_METHODS, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return mixed
     */
    public function getAllowedCustomerGroups(?int $storeId = null)
    {
        return $this->scopeConfig->getValue(self::B2B_ALLOWED_CUSTOMER_GROUPS, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return mixed
     */
    public function getContactEmail(?int $storeId = null)
    {
        return $this->scopeConfig->getValue(self::B2B_ALLOWED_CONTACT_EMAIL, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return mixed
     */
    public function getPaymentDeadline(?int $storeId = null)
    {
        return $this->scopeConfig->getValue(self::B2B_INVOICE_PAYMENT_DEADLINE, ScopeInterface::SCOPE_STORE, $storeId);

    }

    /**
     * @param int|null $storeId
     * @return mixed
     */
    public function getOfferDeadline(?int $storeId = null)
    {
        return $this->scopeConfig->getValue(self::B2B_QUOTE_PAYMENT_DEADLINE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return mixed
     */
    public function getDiscountMaxValue(?int $storeId = null)
    {
        return $this->scopeConfig->getValue(self::B2B_DISCOUNT_MAX_VALUE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return mixed
     */
    public function getDiscountMaxValueCart(?int $storeId = null)
    {
        return $this->scopeConfig->getValue(self::B2B_DISCOUNT_MAX_VALUE_CART, ScopeInterface::SCOPE_STORE, $storeId);

    }
    public function getShippingDiscountEnabled(?int $storeId = null)
    {
        return $this->scopeConfig->getValue(self::B2B_SHIPPING_DISCOUNT_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return mixed
     */
    public function getIsSplitEnabled(?int $storeId = null)
    {
        return $this->scopeConfig->getValue(self::B2B_SPLIT_IS_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return mixed
     */
    public function getInvoiceText(?int $storeId = null)
    {
        return $this->scopeConfig->getValue(self::B2B_SPLIT_INVOICE_TEXT, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param $index
     * @param int|null $storeId
     * @return mixed
     */
    public function getPaymentStatus($index, ?int $storeId = null)
    {
        return $this->scopeConfig->getValue(self::B2B_SPLIT_PAYMENT_STATUS . $index, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param $index
     * @param int|null $storeId
     * @return mixed
     */
    public function getB2BSplitEmailTemplate($index, ?int $storeId = null)
    {
        return $this->scopeConfig->getValue(self::B2B_SPLIT_EMAIL_TEMPLATE . $index, ScopeInterface::SCOPE_STORE, $storeId);

    }

    /**
     * @param int|null $storeId
     * @return mixed
     */
    public function getB2BSplitEmailAddresses(?int $storeId = null)
    {
        return $this->scopeConfig->getValue(self::B2B_SPLIT_EMAIL_ADDRESSES, ScopeInterface::SCOPE_STORE, $storeId);

    }

    /**
     * @param int|null $storeId
     * @return mixed
     */
    public function getB2BSplitDisableStatusEmailSend(?int $storeId = null)
    {
        return $this->scopeConfig->getValue(self::B2B_SPLIT_DISABLE_STATUS_EMAIL_SEND, ScopeInterface::SCOPE_STORE, $storeId);
    }

}
