<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

namespace BelVG\LayoutCustomizer\Plugin\CustomerData;

use Magento\Checkout\CustomerData\Cart as Subject;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject;

/**
 * Class CartSection
 * @package BelVG\LayoutCustomizer\Plugin\CustomerData
 */
class CartSection
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * CartSection constructor.
     * @param Session $checkoutSession
     */
    public function __construct(Session $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param Subject $subject
     * @param $result
     * @return mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterGetSectionData(Subject $subject, $result)
    {
        $quote = $this->checkoutSession->getQuote() ?: new DataObject();
        if ($quote->getData('grand_total')) {
            $result['subtotalAmount'] = round((float) $quote->getData('grand_total'), 2);
        }
        return $result;
    }
}
