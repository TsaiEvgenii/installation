<?php
namespace BelVG\LayoutCustomizer\Controller\Adminhtml\Layout;

use Magento\Framework\Exception\LocalizedException;
use BelVG\LayoutCustomizer\Model\Layout as LayoutModel;

class Save extends \BelVG\LayoutCustomizer\Controller\Adminhtml\Layout
{
    protected $dataPersistor;
    protected $logger;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Psr\Log\LoggerInterface $logger = null
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->logger = $logger;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // Init model
        $model = $this->initModel();
        if (!$model) {
            return $this->createRedirect('*/*/');
        }

        // Get data
        $data = $this->getRequest()->getPostValue();
        // [field1 => '1', field2 => '0', ...]
        $useDefault = (array) $this->getRequest()->getParam('use_default', []);
        foreach ($useDefault as $key => $value) {
            if ($value) {
                $data[$key] = null;
            }
        }

        if (!empty($data)) {
            try {
                // Update and save model
                $model->addData($data);
                if (!$model->getLayoutmaterialId())
                    $model->setData('layoutmaterial_id', null);
                $model->save();

                // Add message
                $this->messageManager->addSuccessMessage(__('You saved the Layout.'));
                // Clear form data
                $this->dataPersistor->clear('belvg_layoutcustomizer_layout');

                // Redirect
                return $this->getRequest()->getParam('back')
                    ? $this->createRedirect('*/*/edit', ['layout_id' => $model->getId()])
                    : $this->createRedirect('*/*/');

            } catch (LocalizedException $e) {
                if ($this->logger) {
                    $this->logger->critical($e);
                }
                $this->messageManager->addErrorMessage($e->getMessage());

            } catch (\Exception $e) {
                if ($this->logger) {
                    $this->logger->critical($e);
                }
                $this->messageManager->addExceptionMessage(
                    $e, __('Something went wrong while saving the Layout.'));
            }

            // Store form data
            $this->dataPersistor->set('belvg_layoutcustomizer_layout', $data);
            // Redirect back
            return $this->createRedirect('*/*/edit', ['layout_id' => $model->getId()]);
        }
        return $this->createRedirect('*/*/');
    }

    protected function createRedirect($path, array $params = [])
    {
        $storeId = $this->getRequest()->getParam('store');
        return parent::createRedirect(
            $path,
            array_merge(['store' => $storeId], $params));
    }
}
