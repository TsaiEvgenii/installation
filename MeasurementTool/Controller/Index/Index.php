<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\MeasurementTool\Controller\Index;

use BelVG\MeasurementTool\Model\MeasurementToolFactory;
use BelVG\MeasurementTool\Model\MeasurementTool as MeasurementToolModel;
use BelVG\MeasurementTool\Model\ResourceModel\MeasurementTool as MeasurementToolResourceModel;
use Magento\Checkout\Controller\Action;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;

class Index extends Action implements HttpGetActionInterface
{
    public function __construct(
        protected MeasurementToolFactory $measurementToolModelFactory,
        protected MeasurementToolResourceModel $measurementToolResourceModel,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
    ) {
        parent::__construct($context, $customerSession, $customerRepository, $accountManagement);
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        if(!$this->_customerSession->isLoggedIn()){
            $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $redirect->setPath('customer/account/login');
            return $redirect;
        }

        $measurementToolId = $this->getRequest()->getParam('measurement_tool_id');
        if ($measurementToolId) {
            /** @var MeasurementToolModel $measurementToolModel */
            $measurementToolModel = $this->measurementToolModelFactory->create();
            $this->measurementToolResourceModel->load($measurementToolModel, $measurementToolId);
            $customerId = $this->_customerSession->getCustomerId();
            if ($measurementToolModel->getData('customer_id') !== $customerId) {
                $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

                $this->messageManager->addNoticeMessage(__('Measurement Tool with ID "%1" doesn\'t exist for the current customer.',
                    $measurementToolId));
                $redirect->setPath('customer/account');
                return $redirect;
            }
        }

        /** @var \Magento\Framework\View\Result\Page $page */
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        return $page;
    }
}
