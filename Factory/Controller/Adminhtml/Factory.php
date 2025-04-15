<?php
namespace BelVG\Factory\Controller\Adminhtml;

use BelVG\Factory\Controller\Adminhtml\Factory\Helper;
use Magento\Backend\App\Action;
use Psr\Log\LoggerInterface;

abstract class Factory extends Action
{
    const ADMIN_RESOURCE = 'BelVG_Factory::factories';

    public function __construct(
        Action\Context $context,
        protected readonly Helper\Factory $factoryHelper,
        protected readonly Helper\Page $pageHelper,
        protected readonly LoggerInterface $logger
    ) {
        parent::__construct($context);
    }

    protected function createRedirect($path, array $params = [])
    {
        return $this->resultRedirectFactory
            ->create()
            ->setPath($path, array_merge(
                ['store' => $this->getRequest()->getParam('store')],
                $params));
    }
}
