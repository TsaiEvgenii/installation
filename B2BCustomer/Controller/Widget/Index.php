<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Controller\Widget;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\InputException;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\ValidationConfigResolverInterface;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use BelVG\B2BCustomer\Model\Service\ContactEmailSender;

class Index extends \Magento\Framework\App\Action\Action
{

    const CAPTCHA_KEY = 'contact';

    /**
     * @var string[]
     */
    protected static $requiredFields = [
        'first_name' => 'First name',
        'last_name' => 'Second name',
        'company_name' => 'Company name',
        'cvr_number' => 'CVR number',
        'email' => 'Email address',
        'phone_number' => 'Phone number'
    ];

    /**
     * @var IsCaptchaEnabledInterface
     */
    protected $isCaptchaEnabled;
    /**
     * @var ValidatorInterface
     */
    protected $captchaValidator;
    /**
     * @var CaptchaResponseResolverInterface
     */
    protected $captchaResponseResolver;
    /**
     * @var ContactEmailSender
     */
    protected $senderService;
    /**
     * @var ValidationConfigResolverInterface
     */
    protected $validationConfigResolver;

    /**
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param ValidatorInterface $captchaValidator
     * @param ContactEmailSender $senderService
     * @param ValidationConfigResolverInterface $validationConfigResolver
     * @param CaptchaResponseResolverInterface $captchaResponseResolver
     * @param Context $context
     */
    public function __construct(
        IsCaptchaEnabledInterface         $isCaptchaEnabled,
        ValidatorInterface                $captchaValidator,
        ContactEmailSender                $senderService,
        ValidationConfigResolverInterface $validationConfigResolver,
        CaptchaResponseResolverInterface  $captchaResponseResolver,
        Context                           $context
    )
    {
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->captchaValidator = $captchaValidator;
        $this->senderService = $senderService;
        $this->validationConfigResolver = $validationConfigResolver;
        $this->captchaResponseResolver = $captchaResponseResolver;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws InputException
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!isset($data['currUrl'])) {
            $this->messageManager->addErrorMessage(__('Something went wrong while sending the contact us request. Please try again.'));
            return $resultRedirect->setRefererUrl();
        }
        $redirectUrl = $data['currUrl'];
        if ($this->isCaptchaEnabled->isCaptchaEnabledFor(self::CAPTCHA_KEY)) {
            $validationConfig = $this->validationConfigResolver->get(self::CAPTCHA_KEY);
            try {
                $reCaptchaResponse = $this->captchaResponseResolver->resolve($this->getRequest());
            } catch (InputException $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setUrl($redirectUrl);
            }
            $validationResult = $this->captchaValidator->isValid($reCaptchaResponse, $validationConfig);
            if (false === $validationResult->isValid()) {
                $this->messageManager->addError($validationResult->getErrors());
                return $resultRedirect->setUrl($redirectUrl);
            }
        }
        try {
            foreach (self::$requiredFields as $requiredField => $requiredFieldTitle) {
                if (!isset($data[$requiredField]) || empty($data[$requiredField])) {
                    throw new InputException(__('"%fieldName" is required. Enter and try again.', $requiredFieldTitle));
                }
            }
            $this->senderService->send($data);
            $this->messageManager->addSuccessMessage(__("Your request has been received. We will respond to you as soon as possible."));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while sending the contact us request. Please try again.'));
        }
        return $resultRedirect->setUrl($redirectUrl);

    }
}
