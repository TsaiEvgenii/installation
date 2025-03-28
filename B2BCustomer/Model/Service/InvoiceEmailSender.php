<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Model\Service;

use BelVG\B2BCustomer\Model\Config;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Email\Container\OrderCommentIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;
use Magento\Store\Model\App\Emulation;
use Magento\Sales\Model\Order\Email\SenderBuilderFactory;

class InvoiceEmailSender extends OrderCommentSender
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;


    /**
     * @param Template $templateContainer
     * @param OrderCommentIdentity $identityContainer
     * @param SenderBuilderFactory $senderBuilderFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param Config $config
     * @param PaymentHelper $paymentHelper
     * @param Renderer $addressRenderer
     * @param ManagerInterface $eventManager
     * @param Emulation|null $appEmulation
     */
    public function __construct(
        Template                 $templateContainer,
        OrderCommentIdentity     $identityContainer,
        SenderBuilderFactory     $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        Config                   $config,
        PaymentHelper            $paymentHelper,
        Renderer                 $addressRenderer,
        ManagerInterface         $eventManager,
        Emulation                $appEmulation = null
    )
    {
        $this->config = $config;
        $this->paymentHelper = $paymentHelper;
        parent::__construct($templateContainer, $identityContainer, $senderBuilderFactory, $logger, $addressRenderer, $eventManager, $appEmulation);
    }

    /**
     * @param Order $order
     * @param $notify
     * @param $comment
     * @return bool
     */
    public function sendEmail(Order $order, $customer)
    {
        $this->identityContainer->setStore($order->getStore());
        if (!$this->identityContainer->isEnabled()) {
            return false;
        }
        $transport = [
            'order' => $order,
            'customer' => $customer,
            'comment' => '',
            'billing' => $order->getBillingAddress(),
            'store' => $order->getStore(),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
            'payment_html' => $this->getPaymentHtml($order)
        ];
        $transportObject = new DataObject($transport);

        $this->eventManager->dispatch(
            'email_order_comment_set_template_vars_before',
            ['sender' => $this, 'transport' => $transport, 'transportObject' => $transportObject]
        );
        $this->templateContainer->setTemplateVars($transportObject->getData());


        $this->_prepareTemplate($order);

        $sender = $this->getSender();
        $sender->send();

        return true;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    protected function _prepareTemplate($order)
    {
        $this->templateContainer->setTemplateOptions($this->getTemplateOptions());

        if ($order->getCustomerIsGuest()) {
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $customerName = $order->getCustomerName();
        }

        $this->identityContainer->setCustomerName($customerName);
        $this->identityContainer->setCustomerEmail($order->getCustomerEmail());
        $this->templateContainer->setTemplateId($this->config->getB2BSplitEmailTemplate($order->getInvoiceCollection()->count()));
    }

    /**
     * @param Order $order
     * @return string
     * @throws \Exception
     */
    protected function getPaymentHtml(Order $order)
    {
        return $this->paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $order->getStore()->getId()
        );
    }
}
