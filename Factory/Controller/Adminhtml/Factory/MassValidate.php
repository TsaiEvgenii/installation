<?php
namespace BelVG\Factory\Controller\Adminhtml\Factory;

use BelVG\Factory\Controller\Adminhtml\Factory\Helper;
use BelVG\Factory\Api\FactoryRepositoryInterface;
use BelVG\Factory\Model\ResourceModel\Factory\CollectionFactory
    as FactoryCollectionFactory;
use BelVG\Factory\Model\Validator;
use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter as MassActionFilter;
use Psr\Log\LoggerInterface;

class MassValidate extends \BelVG\Factory\Controller\Adminhtml\Factory
{
    protected $massActionFilter;
    protected $factoryCollectionFactory;
    protected $factoryRepo;
    protected Validator $validator;

    public function __construct(
        Action\Context $context,
        Helper\Factory $factoryHelper,
        Helper\Page $pageHelper,
        LoggerInterface $logger,
        MassActionFilter $massActionFilter,
        FactoryCollectionFactory $factoryCollectionFactory,
        FactoryRepositoryInterface $factoryRepo,
        Validator $validator)
    {
        parent::__construct($context, $factoryHelper, $pageHelper, $logger);
        $this->massActionFilter = $massActionFilter;
        $this->factoryCollectionFactory = $factoryCollectionFactory;
        $this->factoryRepo = $factoryRepo;
        $this->validator = $validator;
    }

    public function execute()
    {
        try {
            // Init store
            $this->factoryHelper->initStore($this->getRequest());
            $store = $this->factoryHelper->getStore();

            // Get collection
            $collection = $this->massActionFilter->getCollection(
                $this->factoryCollectionFactory->create());

            // Validate
            if ($store->getId() == 0) {
                $this->messageManager->addWarningMessage(
                    __('Select a store to validate delivery rules'));
            }
            foreach ($collection->getAllIds() as $factoryId) {
                $factory = $this->factoryRepo->getById($factoryId, $store->getId());
                $errors = $this->validator->validate($factory, $store);
                if (empty($errors)) {
                    $this->messageManager->addSuccessMessage(
                        __('Factory "%1" is valid', $factory->getName()));
                } else {
                    $this->messageManager->addWarningMessage(
                        __('Factory %1 has following issues:', $factory->getName()));
                    foreach ($errors as $error)
                        $this->messageManager->addWarningMessage(
                            sprintf(' &bull; %s', $error));
                }
            }

        } catch(\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(__('Failed to mass validate factories'));
        }

        return $this->createRedirect('*/*/');
    }
}
