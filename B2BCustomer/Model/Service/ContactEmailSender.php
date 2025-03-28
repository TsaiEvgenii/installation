<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Model\Service;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use BelVG\B2BCustomer\Model\Config;

class ContactEmailSender
{
    /**
     * @var StateInterface
     */
    protected $inlineTranslation;
    /**
     * @var Escaper
     */
    protected $escaper;
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var Config
     */
    protected $config;


    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $inlineTranslation
     * @param Escaper $escaper
     * @param Config $config
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        StateInterface        $inlineTranslation,
        Escaper               $escaper,
        Config                $config,
        ScopeConfigInterface  $scopeConfig,
        TransportBuilder      $transportBuilder
    )
    {
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * @param $data
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function send($data)
    {
        $currentStore = $this->storeManager->getStore();
        $storeId = $currentStore->getId();
        $templateOptions = array(
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $storeId
        );

        $templateVars = array(
            'data' => $data
        );

        $from = [
            'email' => $this->scopeConfig->getValue('trans_email/ident_support/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId),
            'name' => $this->scopeConfig->getValue('trans_email/ident_support/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId)
        ];

        $this->inlineTranslation->suspend();
        $transport = $this->transportBuilder->setTemplateIdentifier('b2b_template')
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($templateVars)
            ->setFrom($from)
            ->addTo($this->config->getContactEmail($storeId))
            ->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();

    }
}
