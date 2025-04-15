<?php
namespace BelVG\Factory\Controller\Adminhtml\Factory;

use BelVG\Factory\Controller\Adminhtml\Factory\Helper;
use Magento\Backend\App\Action;

class Index extends \BelVG\Factory\Controller\Adminhtml\Factory
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->factoryHelper->initStore($this->getRequest());
        return $this->pageHelper->createPage([__('Factories')]);
    }
}
