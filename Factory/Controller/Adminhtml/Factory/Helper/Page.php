<?php
namespace BelVG\Factory\Controller\Adminhtml\Factory\Helper;

use BelVG\Factory\Controller\Adminhtml\Factory as FactoryController;
use Magento\Framework\View\Result\PageFactory;

class Page
{
    protected $pageFactory;

    public function __construct(PageFactory $pageFactory)
    {
        $this->pageFactory = $pageFactory;
    }

    public function createPage($titles = [], $breadcrumbs = [])
    {
        $page = $this->pageFactory
            ->create()
            ->setActiveMenu(FactoryController::ADMIN_RESOURCE)
            ->addBreadcrumb(__('BelVG'), __('BelVG'))
            ->addBreadcrumb(__('Factories'), __('Factories'));
        // Add titles
        foreach ($titles as $title) {
            $page->getConfig()->getTitle()->prepend($title);
        }
        // Add breadcrumbs
        foreach ($breadcrumbs as $breadcrumb) {
            $page->addBreadcrumb($breadcrumb, $breadcrumb);
        }
        return $page;
    }
}
