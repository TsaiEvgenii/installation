<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Model;


use BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface;
use BelVG\MeasurementTool\Api\Data\MeasurementToolInterface;
use BelVG\MeasurementTool\Api\Data\MeasurementToolSearchResultsInterface;
use BelVG\MeasurementTool\Api\Data\MeasurementToolSearchResultsInterfaceFactory;
use BelVG\MeasurementTool\Api\Data\RoomInterface;
use BelVG\MeasurementTool\Api\MeasurementToolImageRepositoryInterface;
use BelVG\MeasurementTool\Api\MeasurementToolRepositoryInterface;
use BelVG\MeasurementTool\Api\RoomRepositoryInterface;
use BelVG\MeasurementTool\Model\ResourceModel\MeasurementTool as ResourceMeasurementToolModel;
use BelVG\MeasurementTool\Model\MeasurementTool as MeasurementToolModel;
use BelVG\MeasurementTool\Model\MeasurementToolFactory;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class MeasurementToolRepository implements MeasurementToolRepositoryInterface
{
    public function __construct(
        protected \BelVG\MeasurementTool\Model\MeasurementToolFactory $measurementToolModelFactory,
        protected ResourceMeasurementToolModel $resourceMeasurementToolModel,
        protected \BelVG\MeasurementTool\Model\ResourceModel\MeasurementTool\CollectionFactory $collectionFactory,
        protected JoinProcessorInterface $extensionAttributesJoinProcessor,
        protected CollectionProcessorInterface $collectionProcessor,
        protected MeasurementToolSearchResultsInterfaceFactory $searchResultsFactory,
        protected MeasurementToolImageRepositoryInterface $measurementToolImageRepository,
        protected FilterBuilder $filterBuilder,
        protected FilterGroupBuilder $filterGroupBuilder,
        protected SearchCriteriaBuilder $searchCriteriaBuilder,
        protected RoomRepositoryInterface $roomRepository,
        protected ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {

    }

    public function save(MeasurementToolInterface $measurementTool): MeasurementToolInterface
    {
        $measurementToolData = $this->extensibleDataObjectConverter->toNestedArray(
            $measurementTool,
            [],
            MeasurementToolInterface::class
        );
        /** @var MeasurementToolModel $measurementToolModel */
        $measurementToolModel = $this->measurementToolModelFactory->create();
        $measurementToolModel->setData($measurementToolData);
        try {
            $this->resourceMeasurementToolModel->save($measurementToolModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the economicCustomer: %1',
                $exception->getMessage()
            ));
        }

        return $measurementToolModel->getDataModel();
    }

    public function getById(int $measurementToolId): MeasurementToolInterface
    {
        /** @var MeasurementToolModel $measurementToolModel */
        $measurementToolModel = $this->measurementToolModelFactory->create();
        $this->resourceMeasurementToolModel->load($measurementToolModel, $measurementToolId);
        if (!$measurementToolModel->getId()) {
            throw new NoSuchEntityException(__('economic_customer with id "%1" does not exist.', $measurementToolId));
        }

        $measurementToolDataModel = $measurementToolModel->getDataModel();
        $this->setRooms($measurementToolDataModel);
        $this->setImages($measurementToolDataModel);

        return $measurementToolDataModel;
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \BelVG\MeasurementTool\Model\ResourceModel\MeasurementTool\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \BelVG\MeasurementTool\Api\Data\MeasurementToolInterface::class
        );
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var MeasurementToolSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $items = [];
        /** @var \BelVG\MeasurementTool\Model\MeasurementTool $model */
        foreach ($collection as $model) {
            /** @var  $measurementToolDataModel */
            $measurementToolDataModel = $model->getDataModel();
            $this->setRooms($measurementToolDataModel);
            $items[] = $measurementToolDataModel;
        }
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @throws LocalizedException
     */
    private function setRooms(MeasurementToolInterface $measurementToolDataModel): void
    {
        $filterMeasurementToolId = $this->filterBuilder
            ->setField(RoomInterface::MEASUREMENT_TOOL_ID)
            ->setValue($measurementToolDataModel->getEntityId())
            ->setConditionType('eq')
            ->create();

        $filterGroupMeasurementToolId = $this->filterGroupBuilder->addFilter($filterMeasurementToolId)->create();
        $criteria = $this->searchCriteriaBuilder->setFilterGroups([$filterGroupMeasurementToolId])->create();
        $roomResult = $this->roomRepository->getList($criteria);
        $measurementToolDataModel->setRooms($roomResult->getItems());
    }

    /**
     * @throws LocalizedException
     */
    private function setImages(MeasurementToolInterface $measurementToolDataModel): void
    {
        $filterMeasurementToolId = $this->filterBuilder
            ->setField(MeasurementToolImageInterface::MEASUREMENT_TOOL_ID)
            ->setValue($measurementToolDataModel->getEntityId())
            ->setConditionType('eq')
            ->create();

        $filterGroupMeasurementToolId = $this->filterGroupBuilder->addFilter($filterMeasurementToolId)->create();
        $criteria = $this->searchCriteriaBuilder->setFilterGroups([$filterGroupMeasurementToolId])->create();
        $measurementToolImageResult = $this->measurementToolImageRepository->getList($criteria);
        $measurementToolDataModel->setImages($measurementToolImageResult->getItems());
    }

    public function delete(MeasurementToolInterface $measurementTool): bool
    {
        try {
            /** @var MeasurementToolModel $measurementToolModel */
            $measurementToolModel = $this->measurementToolModelFactory->create();
            $this->resourceMeasurementToolModel->load($measurementToolModel, $measurementTool->getEntityId());
            $this->resourceMeasurementToolModel->delete($measurementToolModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the economic_customer: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    public function deleteById($measurementToolId): bool
    {
        return $this->delete($this->getById($measurementToolId));
    }
}