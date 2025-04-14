<?php
namespace BelVG\LayoutCustomizer\Controller\Adminhtml\Layout;

class Edit extends \BelVG\LayoutCustomizer\Controller\Adminhtml\Layout
{
    protected $pageFactory;

    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry)
    {
        $this->pageFactory = $pageFactory;
        parent::__construct($context, $coreRegistry);
    }


    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $model = $this->initModel();
        if (!$model) {
            return $this->createRedirect('*/*/');
        }

        $breadcrumb = $model->getId() ? __('Edit Layout') : __('New Layout');
        $title = $model->getId()
            ? __('Edit Layout "%1" (ID: %2)', $model->getIdentifier(), $model->getId())
            : __('New Layout');

        $page = $this->pageFactory->create();
        $this->initPage($page);
        $pageTitle = $page->getConfig()->getTitle();
        $page->addBreadcrumb($breadcrumb, $breadcrumb);
        $pageTitle->prepend(__('Layouts'));
        $pageTitle->prepend($title);

        return $page;
    }
}
