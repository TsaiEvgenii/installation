<?php
/**
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\MageWorxOptionTemplates\Model;

use Magento\AsynchronousOperations\Model\ResourceModel\Operation\OperationRepository;
use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\AsynchronousOperations\Api\Data\OperationInterfaceFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\UrlInterface;
use \Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\InventoryCatalogAdminUi\Model\BulkOperationsConfig;
use Magento\Framework\Bulk\OperationInterface as BulkOperationInterface;

/**
 * Class ScheduleBulk
 */
class ScheduleBulk
{
    const TOPIC = 'option_templates.save';
    const OPERATION_KEY = 0;
    /**
     * @var BulkManagementInterface
     */
    private $bulkManagement;

    /**
     * @var IdentityGeneratorInterface
     */
    private $identityService;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;
    /**
     * @var BulkOperationsConfig
     */
    private $bulkOperationsConfig;
    /**
     * @var OperationInterfaceFactory
     */
    private $operationFactory;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * ScheduleBulk constructor.
     *
     * @param BulkManagementInterface $bulkManagement
     * @param BulkOperationsConfig $bulkOperationsConfig
     * @param OperationRepository $operationRepository
     * @param IdentityGeneratorInterface $identityService
     * @param UserContextInterface $userContextInterface
     * @param UrlInterface $urlBuilder
     * @param SerializerInterface $serializer
     * @param OperationInterfaceFactory $operationFactory
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        BulkManagementInterface $bulkManagement,
        BulkOperationsConfig $bulkOperationsConfig,
        IdentityGeneratorInterface $identityService,
        UserContextInterface $userContextInterface,
        UrlInterface $urlBuilder,
        SerializerInterface $serializer,
        OperationInterfaceFactory $operationFactory,
        EntityManager $entityManager,
        JsonHelper $jsonHelper
    )
    {
        $this->userContext = $userContextInterface;
        $this->bulkManagement = $bulkManagement;
        $this->identityService = $identityService;
        $this->urlBuilder = $urlBuilder;
        $this->jsonHelper = $jsonHelper;
        $this->bulkOperationsConfig = $bulkOperationsConfig;
        $this->operationFactory = $operationFactory;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
     * Schedule new bulk operation
     *
     * @param array $operationData
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($operationData)
    {
        $operationCount = count($operationData);
        if ($operationCount > 0) {
            $bulkUuid = $this->identityService->generateId();
            $bulkDescription = 'Mageworx options templates update';
            $bacthes = \array_chunk($operationData, $this->bulkOperationsConfig->getBatchSize());
            $operations = [];
            $userId = $this->userContext->getUserId();
            $this->bulkManagement->scheduleBulk($bulkUuid, [], $bulkDescription, $userId);
            foreach ($bacthes as $batch) {
                $data = [];
                foreach ($batch as $key => $operationItem) {
                    $data[] = [
                        'productId' => $operationItem['productId'],
                        'saveMode' => $operationItem['saveMode'],
                        'oldGroupCustomOptions' => $operationItem['oldGroupCustomOptions'],
                        'groupId' => $operationItem['groupId'],
                        'storeId' => $operationItem['storeId'],
                        'action' => $operationItem['action'],
                        'options' => $operationItem['options'],
                        'meta_information' => 'Update options for product id ' . $operationItem['productId'],
                    ];

                }
                /**
                 * @var OperationInterface $operationModel
                 */
                $operationModel = $this->operationFactory->create();
                $operationModel->setBulkUuid($bulkUuid);
                $operationModel->setTopicName(self::TOPIC);
                $operationModel->setSerializedData($this->serializer->serialize($data));
                /**
                 * @var OperationInterface $operation
                 */
                $operation = $this->entityManager->save(
                    $operationModel,
                    [
                        BulkOperationInterface::ID => self::OPERATION_KEY,
                        BulkOperationInterface::STATUS => BulkOperationInterface::STATUS_TYPE_OPEN
                    ]
                );
                $operations[] = $operation;
            }
            if (sizeof($operations)) {
                $result = $this->bulkManagement->scheduleBulk($bulkUuid, $operations, $bulkDescription, $userId);
                if (!$result) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Can\'t create message queue')
                    );
                }
            }
        }
    }
}
