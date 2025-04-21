<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Controller\Index;


use BelVG\MeasurementTool\Model\Service\AddMeasurementToolElementsToQuote;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Build extends Action implements HttpGetActionInterface
{
    public function __construct(
        protected AddMeasurementToolElementsToQuote $addMeasurementToolElementsToQuoteService,
        protected Session $customerSession,
        Context $context
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->customerSession->isLoggedIn()) {
            $redirect->setPath('customer/account/login');
            return $redirect;
        }

        $measurementToolId = (int)$this->getRequest()->getParam('measurement_tool_id');
        if ($measurementToolId) {
            $this->addMeasurementToolElementsToQuoteService->add($measurementToolId);
            $this->messageManager->addSuccessMessage(__('Measurement Tool elements were added.'));
        }
        $redirect->setPath('checkout/cart/index');
        return $redirect;
    }
}
