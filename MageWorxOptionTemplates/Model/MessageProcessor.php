<?php
/**
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\MageWorxOptionTemplates\Model;

use Magento\Framework\Bulk\OperationInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\TemporaryStateExceptionInterface;
use Magento\Framework\MessageQueue\ConsumerConfigurationInterface;
use Magento\Framework\MessageQueue\MessageStatusProcessor;
use Magento\Framework\MessageQueue\QueueInterface;
use Magento\Framework\Registry;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Serialize\SerializerInterface;
use MageWorx\OptionBase\Model\ResourceModel\CollectionUpdaterRegistry;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class MessageProcessor
 * @package BelVG\MageWorxOptionTemplates\Model
 */
class MessageProcessor
{

    /**
     * @var \Magento\Framework\MessageQueue\MessageStatusProcessor
     */
    protected $messageStatusProcessor;

    /**
     * @var OptionSaver
     */
    protected $optionSaver;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var CollectionUpdaterRegistry
     */
    protected $collectionUpdaterRegistry;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var Registry
     */
    protected $registry;

    protected $infoLogger;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * MessageProcessor constructor.
     * @param Registry $registry
     * @param MessageStatusProcessor $messageStatusProcessor
     * @param CollectionFactory $productCollectionFactory
     * @param CollectionUpdaterRegistry $collectionUpdaterRegistry
     * @param OptionSaver $optionSaver
     * @param Log\InfoLogger $infoLogger
     * @param Logger $logger
     * @param SerializerInterface $serializer
     * @param JsonHelper $jsonHelper
     * @param EntityManager $entityManager
     */
    public function __construct(
        Registry $registry,
        MessageStatusProcessor $messageStatusProcessor,
        CollectionFactory $productCollectionFactory,
        CollectionUpdaterRegistry $collectionUpdaterRegistry,
        OptionSaver $optionSaver,
        Log\InfoLogger $infoLogger,
        Logger $logger,
        SerializerInterface $serializer,
        JsonHelper $jsonHelper,
       EntityManager $entityManager
    )
    {
        $this->registry = $registry;
        $this->jsonHelper = $jsonHelper;
        $this->optionSaver = $optionSaver;
        $this->infoLogger = $infoLogger;
        $this->logger = $logger;
        $this->messageStatusProcessor = $messageStatusProcessor;
        $this->collectionUpdaterRegistry = $collectionUpdaterRegistry;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
     * Processing decoded messages, invoking callbacks, changing statuses for messages.
     *
     * @param QueueInterface $queue
     * @param ConsumerConfigurationInterface $configuration
     * @param array $messages
     * @param array $messagesToAcknowledge
     * @param array $mergedMessages
     * @return void
     */
    public function process(OperationInterface $operation)
    {
        try{
            $serializedData = $operation->getSerializedData();
            $data = $this->serializer->unserialize($serializedData);
            $this->dispatchMessages($data);
        }
        catch (\Zend_Db_Adapter_Exception $e) {
            $this->logger->critical($e->getMessage());
            if ($e instanceof \Magento\Framework\DB\Adapter\LockWaitException
                || $e instanceof \Magento\Framework\DB\Adapter\DeadlockException
                || $e instanceof \Magento\Framework\DB\Adapter\ConnectionException
            ) {
                $status = OperationInterface::STATUS_TYPE_RETRIABLY_FAILED;
                $errorCode = $e->getCode();
                $message = $e->getMessage();
            } else {
                $status = OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED;
                $errorCode = $e->getCode();
                $message = __(
                    'Sorry, something went wrong during product attributes update. Please see log for details.'
                );
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->critical($e->getMessage());
            $status = ($e instanceof TemporaryStateExceptionInterface)
                ? OperationInterface::STATUS_TYPE_RETRIABLY_FAILED
                : OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED;
            $errorCode = $e->getCode();
            $message = $e->getMessage();
        } catch (LocalizedException $e) {
            $this->logger->critical($e->getMessage());
            $status = OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED;
            $errorCode = $e->getCode();
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $status = OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED;
            $errorCode = $e->getCode();
            $message = __('Sorry, something went wrong during update. Please see log for details.');
        }

        $operation->setStatus($status ?? OperationInterface::STATUS_TYPE_COMPLETE)
            ->setErrorCode($errorCode ?? null)
            ->setResultMessage($message ?? null);

        $this->entityManager->save($operation);
    }

    /**
     * Processing decoded messages, invoking callbacks, changing statuses for messages.
     *
     * @param QueueInterface $queue
     * @param ConsumerConfigurationInterface $configuration
     * @param array $messageList
     */
    public function dispatchMessages(
        array $products = []
    )
    {
        //because of constant process product actions are never deleted
        $this->optionSaver->setEmptyActions();
        $groupsProducts = [];
        $groupActions = [];
        foreach ($products as $product){
            $groupId = $product['groupId'];
            $groupActions[$groupId][$product['action']][] = $product['productId'];
            $groupsProducts[$groupId][$product['storeId']]['productIds'][$product['productId']]['product'] = $product['productId'];
            $groupsProducts[$groupId][$product['storeId']]['options'] = $product['options'];
            $groupsProducts[$groupId][$product['storeId']]['saveMode'] = $product['saveMode'];
            $groupsProducts[$groupId][$product['storeId']]['oldGroupCustomOptions'] = $product['oldGroupCustomOptions'];
        }
        foreach ($groupsProducts as $groupId => $groupStore) {
            foreach ($groupStore as $storeId => $group) {
                $this->infoLogger->addProcessorInfoLog($storeId, $groupId, array_keys($group['productIds']));
                $this->optionSaver->setEmptyActions();
                foreach ($groupActions[$groupId] as $groupAction => $actionProductIds) {
                    $this->optionSaver->addProductToAction($actionProductIds, $groupAction);
                }
                $collection = $this->productCollectionFactory->create();
                $this->collectionUpdaterRegistry->setCurrentEntityType('group');
                $this->collectionUpdaterRegistry->setCurrentEntityId($groupId);
                $this->registry->unregister('mageworx_optiontemplates_group_id');
                $this->registry->register('mageworx_optiontemplates_group_id', $groupId);
                $collection->addStoreFilter(0)
                    ->setStoreId(0)
                    ->addFieldToFilter('entity_id', ['in' => $group['productIds']])
                    ->addOptionsToResult();
                $this->optionSaver->updateFromQueue($collection, $group['saveMode'], $groupId, $group['oldGroupCustomOptions'], $group['options'], $storeId);

            }
        }
    }

}
