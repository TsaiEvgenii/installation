<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */
declare(strict_types=1);

namespace BelVG\B2BCustomer\Plugin\Button;

use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Button\Toolbar;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class AddForceInvoiceButton
{

    private const LOG_PREFIX = '[BelVG_B2BCustomer::AddForceInvoiceButton]: ';

    private $logger;

    public function __construct(
        LoggerInterface $logger,

    )
    {
        $this->logger = $logger;
    }

    /**
     * @param Toolbar $subject
     * @param AbstractBlock $context
     * @param ButtonList $buttonList
     * @return void
     */
    public function beforePushButtons(
        Toolbar       $subject,
        AbstractBlock $context,
        ButtonList    $buttonList
    )
    {
        try {
            $order = $context->getOrder();
            $nameInLayout = $context->getNameInLayout();
            if ('sales_order_edit' == $nameInLayout && $order->getState() === Order::STATE_COMPLETE && !$order->getInvoiceCollection()) {
                $buttonUrl = $context->getUrl('belvg_b2bcustomer/order/forceinvoice', ['order_id' => $order->getId()]);
                $buttonList->add(
                    'force_invoice',
                    [
                        'label' => __('Force Invoice'),
                        'class' => 'force-invoice',
                        'onclick' => 'window.location.href = \'' . $buttonUrl . '\'',
                        'sort_order' => 100
                    ]
                );
            }
        } catch (\Throwable $throwable) {
            $this->logger->error(
                sprintf(
                    self::LOG_PREFIX . $throwable->getMessage()
                )
            );
        }
    }

}
