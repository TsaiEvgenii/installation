<?php


namespace BelVG\LayoutCustomizer\Controller\Adminhtml\Layout;


class FileUpload extends \BelVG\LayoutCustomizer\Controller\Adminhtml\Layout
{
    /**
     * @var \BelVG\LayoutCustomizer\Model\Config\FileProcessor
     */
    private $fileProcessor;

    public function __construct(
        \BelVG\LayoutCustomizer\Model\Config\FileProcessor $fileProcessor,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->fileProcessor = $fileProcessor;

        parent::__construct($context, $coreRegistry);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $result = $this->fileProcessor->save(key($_FILES));
        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)->setData($result);
    }
}