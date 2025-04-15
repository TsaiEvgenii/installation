<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Controller\Adminhtml\Grid;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use BelVG\Factory\Api\FactoryRepositoryInterface;

class InlineEdit extends Action
{
    /**
     * @param Context $context
     * @param RedirectInterface $redirect
     * @param FactoryRepositoryInterface $factoryRepository
     */
    public function __construct(
        Context $context,
        private readonly RedirectInterface $redirect,
        private readonly FactoryRepositoryInterface $factoryRepository
    ) {
        parent::__construct($context);
    }

    /**
     * @return Json
     */
    public function execute(): Json
    {
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $error = false;
        $messages = [];
        if ($this->getRequest()->getParam('isAjax')) {

            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {

                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {

                foreach (array_keys($postItems) as $modelId) {
                    try {
                        $storeId = $this->getStoreId();
                        $factoryDto = $this->factoryRepository->getById($modelId, $storeId);

                        foreach ($postItems[$modelId] as $key => $value) {
                            $factoryDto->setData($key, $value);
                        }

                        $this->factoryRepository->save($factoryDto, $storeId);
                    } catch (\Exception $e) {
                        $messages[] = "[Factory ID : $modelId]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }

        return $response->setData([
            'messages' => $messages,
            'error' => $error,
        ]);
    }

    /**
     * @return int
     */
    private function getStoreId(): int
    {
        $storeId = 0;
        $refererUrl = $this->redirect->getRefererUrl();
        if ($refererUrl && str_contains($refererUrl, 'store')) {

            $pattern = '/store\/(\d+)/';
            $matches = [];
            preg_match($pattern, $refererUrl, $matches);

            if (!empty($matches[1])) {
                $storeId = (int) $matches[1];
            }
        }

        return $storeId;
    }
}
