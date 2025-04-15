<?php
/**
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\MageWorxOptionTemplates\Model;

use BelVG\MageWorxOptionTemplates\Model\Log\Item as LogItem;
use BelVG\MageWorxOptionTemplates\Model\Log\ItemFactory as LogItemFactory;
use BelVG\MageWorxOptionTemplates\Model\ResourceModel\Log\Item as LogItemResource;
use Psr\Log\LoggerInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class Logger
 * @package BelVG\MageWorxOptionTemplates\Model
 */
class Logger
{
    /**
     *
     */
    const TAG = 'option_templates';

    /**
     * @var LogItemFactory
     */
    protected $logItemFactory;
    /**
     * @var LogItemResource
     */
    protected $logItemResource;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;


    /**
     * Logger constructor.
     * @param LogItemFactory $logItemFactory
     * @param LogItemResource $logItemResource
     * @param LoggerInterface $logger
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        LogItemFactory $logItemFactory,
        LogItemResource $logItemResource,
        LoggerInterface $logger,
        JsonHelper $jsonHelper
    )
    {
        $this->logItemFactory = $logItemFactory;
        $this->logItemResource = $logItemResource;
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
    }


    /**
     * @param $storeId
     * @param $type
     * @param null $groupId
     * @param array|null $productIds
     */
    public function cannotProcess($storeId, $type, $groupId = null, array $productIds = null)
    {
        // Log to file
        $this->logger->critical(sprintf('[%s] %s', self::TAG, $this->queueToString($storeId, $type, $groupId, $productIds)));

        // Log to DB
        try {
            $logItem = $this->logItemFactory->create();
            $logItem->setEventType($type)
                ->setGroupId($groupId)
                ->setProductIds($this->jsonHelper->jsonEncode($productIds))
                ->setStoreId($storeId);
            $this->logItemResource->save($logItem);
        } catch (\Exception $e) {
            $this->logger->critical(
                sprintf('[%s] Failed to add DB log item: %s', self::TAG, $e->getMessage()));
        }
    }


    /**
     * @param $storeId
     * @param $type
     * @param null $groupId
     * @param array|null $productIds
     * @return string
     */
    protected function queueToString($storeId, $type, $groupId = null, array $productIds = null)
    {
        if ($type == LogItem::QUEUE_CONSUME) {
            return sprintf(
                'Cannot consume queue for MageWorx options template: %s, store: %s, items: %s',
                $groupId, $storeId, $this->jsonHelper->jsonEncode($productIds));
        } else {
            return sprintf(
                'Cannot publish queue for MageWorx options template: %s, store: %s',
                $groupId, $storeId);
        }

    }
}
