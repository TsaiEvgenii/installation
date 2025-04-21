<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2025.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Controller\Element;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;

class UploadImage extends  \Magento\Framework\App\Action\Action implements HttpPostActionInterface
{
    public function __construct(
        Context $context,
        protected \Magento\Catalog\Model\ImageUploader $imageUploader
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $imageId = $this->_request->getParam('param_name', 'image');

        try {
            $result = $this->imageUploader->saveFileToTmpDir($imageId);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}