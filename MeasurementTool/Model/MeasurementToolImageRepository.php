<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Model;


use BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface;
use BelVG\MeasurementTool\Api\Data\MeasurementToolImageSearchResultsInterfaceFactory;
use BelVG\MeasurementTool\Api\MeasurementToolImageRepositoryInterface;
use BelVG\MeasurementTool\Model\MeasurementToolImg as MeasurementToolImgModel;
use BelVG\MeasurementTool\Model\ResourceModel\MeasurementToolImg as ResourceMeasurementToolImgModel;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

class MeasurementToolImageRepository implements MeasurementToolImageRepositoryInterface
{
    public function __construct(
        protected \BelVG\MeasurementTool\Model\MeasurementToolImgFactory $measurementToolImgModelFactory,
        protected ResourceMeasurementToolImgModel $resourceMeasurementToolImgModel,
        protected \BelVG\MeasurementTool\Model\ResourceModel\MeasurementToolImg\CollectionFactory $collectionFactory,
        protected JoinProcessorInterface $extensionAttributesJoinProcessor,
        protected CollectionProcessorInterface $collectionProcessor,
        protected MeasurementToolImageSearchResultsInterfaceFactory $searchResultsFactory,
        protected ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {

    }

    public function save(MeasurementToolImageInterface $img): MeasurementToolImageInterface
    {
        $measurementToolImgData = $this->extensibleDataObjectConverter->toNestedArray(
            $img,
            [],
            MeasurementToolImageInterface::class
        );

        /** @var MeasurementToolImg $measurementToolImgModel */
        $measurementToolImgModel = $this->measurementToolImgModelFactory->create();
        $measurementToolImgModel->setData($measurementToolImgData);
        try {
            $this->resourceMeasurementToolImgModel->save($measurementToolImgModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the measurementToolImgModel: %1',
                $exception->getMessage()
            ));
        }

        return $measurementToolImgModel->getDataModel();
    }

    public function getById(int $imgId): MeasurementToolImageInterface
    {
        /** @var MeasurementToolImgModel $measurementToolImgModel */
        $measurementToolImgModel = $this->measurementToolImgModelFactory->create();
        $this->resourceMeasurementToolImgModel->load($measurementToolImgModel, $imgId);

        return $measurementToolImgModel->getDataModel();
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \BelVG\MeasurementTool\Model\ResourceModel\MeasurementToolImg\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \BelVG\MeasurementTool\Api\Data\MeasurementToolImageInterface::class
        );

        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    public function delete(MeasurementToolImageInterface $img): bool
    {
        try {
            /** @var MeasurementToolImgModel $measurementToolImgModel */
            $measurementToolImgModel = $this->measurementToolImgModelFactory->create();
            $this->resourceMeasurementToolImgModel->load($measurementToolImgModel, $img->getEntityId());
            $this->resourceMeasurementToolImgModel->delete($measurementToolImgModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the economic_customer: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    public function deleteById($imgId): bool
    {
        return $this->delete($this->getById($imgId));
    }
}