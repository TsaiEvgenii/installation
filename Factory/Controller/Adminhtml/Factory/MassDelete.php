<?php
namespace BelVG\Factory\Controller\Adminhtml\Factory;

use BelVG\Factory\Controller\Adminhtml\Factory\Helper;
use BelVG\Factory\Api\FactoryRepositoryInterface;
use BelVG\Factory\Model\ResourceModel\Factory\CollectionFactory
    as FactoryCollectionFactory;
use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter as MassActionFilter;
use Psr\Log\LoggerInterface;

class MassDelete extends \BelVG\Factory\Controller\Adminhtml\Factory
{
    protected $massActionFilter;
    protected $factoryCollectionFactory;
    protected $factoryRepo;

    public function __construct(
        Action\Context $context,
        Helper\Factory $factoryHelper,
        Helper\Page $pageHelper,
        LoggerInterface $logger,
        MassActionFilter $massActionFilter,
        FactoryCollectionFactory $factoryCollectionFactory,
        FactoryRepositoryInterface $factoryRepo)
    {
        parent::__construct($context, $factoryHelper, $pageHelper, $logger);
        $this->massActionFilter = $massActionFilter;
        $this->factoryCollectionFactory = $factoryCollectionFactory;
        $this->factoryRepo = $factoryRepo;
    }

    public function execute()
    {
        try {
            // Delete factories
            $collection = $this->massActionFilter->getCollection(
                $this->factoryCollectionFactory->create());
            foreach ($collection as $factoryModel) {
                $this->factoryRepo->deleteById($factoryModel->getId());
            }

            // Add success message
            $this->messageManager->addSuccessMessage(
                __('A total of %1 item(s) have been deleted', count($collection)));

        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(__('Failed to mass delete factories'));
        }

        return $this->createRedirect('*/*/');
    }
}
