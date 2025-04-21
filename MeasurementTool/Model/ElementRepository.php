<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Model;


use BelVG\MeasurementTool\Api\Data\ElementSearchResultsInterfaceFactory;
use BelVG\MeasurementTool\Api\Data\ElementInterface;
use BelVG\MeasurementTool\Api\ElementRepositoryInterface;
use BelVG\MeasurementTool\Model\Element as ElementModel;
use BelVG\MeasurementTool\Model\ElementFactory;
use BelVG\MeasurementTool\Model\ResourceModel\Element as ResourceElementModel;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ElementRepository implements ElementRepositoryInterface
{
    public function __construct(
        protected \BelVG\MeasurementTool\Model\ElementFactory $elementModelFactory,
        protected ResourceElementModel $resourceElementModel,
        protected \BelVG\MeasurementTool\Model\ResourceModel\Element\CollectionFactory $collectionFactory,
        protected JoinProcessorInterface $extensionAttributesJoinProcessor,
        protected CollectionProcessorInterface $collectionProcessor,
        protected ElementSearchResultsInterfaceFactory $searchResultsFactory,
        protected ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {

    }

    public function save(ElementInterface $element): ElementInterface
    {
        $elementData = $this->extensibleDataObjectConverter->toNestedArray(
            $element,
            [],
            ElementInterface::class
        );
        /** @var ElementModel $elementModel */
        $elementModel = $this->elementModelFactory->create();
        $elementModel->setData($elementData);
        try {
            $this->resourceElementModel->save($elementModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the economicCustomer: %1',
                $exception->getMessage()
            ));
        }

        return $elementModel->getDataModel();
    }

    public function getById(int $elementId): ElementInterface
    {
        /** @var ElementModel $elementModel */
        $elementModel = $this->elementModelFactory->create();
        $this->resourceElementModel->load($elementModel, $elementId);
        if (!$elementModel->getId()) {
            throw new NoSuchEntityException(__('economic_customer with id "%1" does not exist.', $elementId));
        }

        return $elementModel->getDataModel();
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \BelVG\MeasurementTool\Model\ResourceModel\Element\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \BelVG\MeasurementTool\Api\Data\ElementInterface::class
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

    public function delete(ElementInterface $element)
    {
        try {
            /** @var ElementModel $elementModel */
            $elementModel = $this->elementModelFactory->create();
            $this->resourceElementModel->load($elementModel, $element->getEntityId());
            $this->resourceElementModel->delete($elementModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the economic_customer: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    public function deleteById($elementId)
    {
        return $this->delete($this->getById($elementId));
    }
}