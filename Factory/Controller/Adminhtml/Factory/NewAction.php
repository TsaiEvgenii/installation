<?php
namespace BelVG\Factory\Controller\Adminhtml\Factory;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;

class NewAction extends Action
{
    public function __construct(
        Action\Context $context,
        protected readonly ForwardFactory $forwardFactory
    ) {
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        return $this->forwardFactory
            ->create()
            ->forward('edit');
    }
}
