<?php
namespace BelVG\LayoutCustomizer\Controller\Adminhtml\Layout;

use Magento\Store\Model\StoreManagerInterface;
use BelVG\LayoutCustomizer\Model\Layout as LayoutModel;

class InlineEdit extends \BelVG\LayoutCustomizer\Controller\Adminhtml\Layout
{
    protected $jsonFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry)
    {
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Inline edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                $store = $this->initStore();

                foreach (array_keys($postItems) as $modelid) {
                    /** @var \BelVG\LayoutCustomizer\Model\Layout $model */
                    $model = $this->_objectManager->create(LayoutModel::class)
                        ->setStoreId($store->getId())
                        ->load($modelid);
                    try {
                        $model->setData(array_merge($model->getData(), $postItems[$modelid]));
                        $model->save();
                    } catch (\Exception $e) {
                        $messages[] = "[Layout ID: {$modelid}]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
