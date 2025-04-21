<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Model;


use BelVG\MeasurementTool\Api\Data\ElementInterface;
use BelVG\MeasurementTool\Api\Data\RoomSearchResultsInterface;
use BelVG\MeasurementTool\Api\Data\RoomSearchResultsInterfaceFactory;
use BelVG\MeasurementTool\Api\Data\RoomInterface;
use BelVG\MeasurementTool\Api\ElementRepositoryInterface;
use BelVG\MeasurementTool\Api\RoomRepositoryInterface;
use BelVG\MeasurementTool\Model\Room as RoomModel;
use BelVG\MeasurementTool\Model\RoomFactory;
use BelVG\MeasurementTool\Model\ResourceModel\Room as ResourceRoomModel;
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

class RoomRepository implements RoomRepositoryInterface
{
    public function __construct(
        protected \BelVG\MeasurementTool\Model\RoomFactory $roomModelFactory,
        protected ResourceRoomModel $resourceRoomModel,
        protected \BelVG\MeasurementTool\Model\ResourceModel\Room\CollectionFactory $collectionFactory,
        protected JoinProcessorInterface $extensionAttributesJoinProcessor,
        protected CollectionProcessorInterface $collectionProcessor,
        protected ElementRepositoryInterface $elementRepository,
        protected FilterBuilder $filterBuilder,
        protected FilterGroupBuilder $filterGroupBuilder,
        protected SearchCriteriaBuilder $searchCriteriaBuilder,
        protected RoomSearchResultsInterfaceFactory $searchResultsFactory,
        protected ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {

    }

    public function save(RoomInterface $room): RoomInterface
    {
        $roomData = $this->extensibleDataObjectConverter->toNestedArray(
            $room,
            [],
            RoomInterface::class
        );
        /** @var RoomModel $roomModel */
        $roomModel = $this->roomModelFactory->create();
        $roomModel->setData($roomData);
        try {
            $this->resourceRoomModel->save($roomModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the economicCustomer: %1',
                $exception->getMessage()
            ));
        }

        return $roomModel->getDataModel();
    }

    public function getById(int $roomId): RoomInterface
    {
        /** @var RoomModel $roomModel */
        $roomModel = $this->roomModelFactory->create();
        $this->resourceRoomModel->load($roomModel, $roomId);
        if (!$roomModel->getId()) {
            throw new NoSuchEntityException(__('economic_customer with id "%1" does not exist.', $roomId));
        }

        return $roomModel->getDataModel();
    }

    /**
     * @inheritDoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \BelVG\MeasurementTool\Model\ResourceModel\Room\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \BelVG\MeasurementTool\Api\Data\RoomInterface::class
        );
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var RoomSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $items = [];
        foreach ($collection as $model) {
            /** @var RoomInterface $roomDataModel */
            $roomDataModel = $model->getDataModel();
            $this->setElements($roomDataModel);
            $items[] = $roomDataModel;
        }
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @throws LocalizedException
     */
    private function setElements(RoomInterface $roomDataModel): void
    {
        $filterRoomId = $this->filterBuilder
            ->setField(ElementInterface::ROOM_ID)
            ->setValue($roomDataModel->getEntityId())
            ->setConditionType('eq')
            ->create();

        $filterGroupRoomId = $this->filterGroupBuilder->addFilter($filterRoomId)->create();
        $criteria = $this->searchCriteriaBuilder->setFilterGroups([$filterGroupRoomId])->create();
        $elementResult = $this->elementRepository->getList($criteria);
        $roomDataModel->setElements($elementResult->getItems());
    }

    public function delete(RoomInterface $room): bool
    {
        try {
            /** @var RoomModel $roomModel */
            $roomModel = $this->roomModelFactory->create();
            $this->resourceRoomModel->load($roomModel, $room->getEntityId());
            $this->resourceRoomModel->delete($roomModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the economic_customer: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    public function deleteById($roomId): bool
    {
        return $this->delete($this->getById($roomId));
    }
}