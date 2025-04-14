<?php
namespace BelVG\LayoutCustomizer\Controller\Adminhtml\Layout;

class Index extends \BelVG\LayoutCustomizer\Controller\Adminhtml\Layout
{
    protected $pageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry)
    {
        $this->pageFactory = $pageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->initStore();
        $page = $this->pageFactory->create();
        $this->initPage($page);
        $page->getConfig()->getTitle()->prepend(__('Layouts'));
        return $page;
    }
}
