<?php
namespace BelVG\LayoutCustomizer\Controller\Adminhtml;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use BelVG\LayoutCustomizer\Model\Layout as LayoutModel;

abstract class Layout extends \Magento\Backend\App\Action
{

    protected $_coreRegistry;
    const ADMIN_RESOURCE = 'BelVG_LayoutCustomizer::top_level';

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('BelVG'), __('BelVG'))
            ->addBreadcrumb(__('Layout'), __('Layout'));
        return $resultPage;
    }

    protected function initStore()
    {
        // Set current store
        $storeManager = $this->_objectManager->get(StoreManagerInterface::class);
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $store = $storeManager->getStore($storeId);
        $storeManager->setCurrentStore($store->getCode());
        return $store;
    }

    protected function initModel()
    {
        try {
            // Init model
            $id = $this->getRequest()->getParam('layout_id');
            $model = $this->_objectManager->create(LayoutModel::class);

            $store = $this->initStore();

            // Load model
            $model->setStoreId($store->getId());
            if ($id) {
                $model->load($id);
                if (!$model->getId()) {
                    throw new LocalizedException(__('This layout no longer exists'));
                }
            }

            // Store model in registry
            $this->_coreRegistry->register('belvg_layoutcustomizer_layout', $model);

            return $model;

        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e, __('Something went wrong while loading the Layout.'));
        }
        return false;
    }

    protected function createRedirect($path, array $params = [])
    {
        return $this->resultRedirectFactory->create()->setPath($path, $params);
    }
}
