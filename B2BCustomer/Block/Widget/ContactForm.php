<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Block\Widget;

use Magento\Framework\View\Element\Template;
use BelVG\B2BCustomer\Model\Config;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template\Context;

class ContactForm extends Template implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = "widget/contactform.phtml";

    /**
     * @var Config
     */
    protected $config;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config  $config,
        array   $data = []
    )
    {
        $this->config = $config;
        $this->_urlBuilder = $context->getUrlBuilder();

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl();

    }

    /**
     * @return string
     */
    public function getFormActionUrl() {
        return $this->getUrl('belvg_b2bcustomer/widget/index');
    }


    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRecaptchaBlock()
    {
        return $this->getLayout()->createBlock('Magento\ReCaptchaUi\Block\ReCaptcha', 'b2b_recaptcha',
            ['data' => ['jsLayout' => ['components' => ['recaptcha' => ['component' => 'Magento_ReCaptchaFrontendUi/js/reCaptcha']]]]])
            ->setData('ifconfig', 'recaptcha_frontend/type_for/contact')
            ->setData('recaptcha_for', 'contact')
            ->setData('jsLayout', ['components' => ['recaptcha' => ['component' => 'Magento_ReCaptchaFrontendUi/js/reCaptcha']]])
            ->setTemplate('Magento_ReCaptchaFrontendUi::recaptcha.phtml')
            ->toHtml();
    }
}
