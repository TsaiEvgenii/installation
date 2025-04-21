<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Model;


use BelVG\MeasurementTool\Api\Data\CustomerElementInterface;
use BelVG\MeasurementTool\Api\Data\CustomerElementSearchResultsInterfaceFactory;
use BelVG\MeasurementTool\Api\CustomerElementRepositoryInterface;
use BelVG\MeasurementTool\Model\CustomerElement as CustomerElementModel;
use BelVG\MeasurementTool\Model\CustomerElementFactory;
use BelVG\MeasurementTool\Model\ResourceModel\CustomerElement as ResourceCustomerElementModel;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CustomerElementRepository implements CustomerElementRepositoryInterface
{
    public function __construct(
        protected \BelVG\MeasurementTool\Model\CustomerElementFactory $customerElementModelFactory,
        protected ResourceCustomerElementModel $resourceCustomerElementModel,
        protected \BelVG\MeasurementTool\Model\ResourceModel\CustomerElement\CollectionFactory $collectionFactory,
        protected JoinProcessorInterface $extensionAttributesJoinProcessor,
        protected CollectionProcessorInterface $collectionProcessor,
        protected CustomerElementSearchResultsInterfaceFactory $searchResultsFactory,
        protected ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {

    }

    public function save(CustomerElementInterface $element): CustomerElementInterface
    {
        $elementData = $this->extensibleDataObjectConverter->toNestedArray(
            $element,
            [],
            CustomerElementInterface::class
        );
        /** @var CustomerElementModel $elementModel */
        $elementModel = $this->customerElementModelFactory->create();
        $elementModel->setData($elementData);
        try {
            $this->resourceCustomerElementModel->save($elementModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the economicCustomer: %1',
                $exception->getMessage()
            ));
        }

        return $elementModel->getDataModel();
    }

    public function getById(int $elementId): CustomerElementInterface
    {
        /** @var CustomerElementModel $elementModel */
        $elementModel = $this->customerElementModelFactory->create();
        $this->resourceCustomerElementModel->load($elementModel, $elementId);

        return $elementModel->getDataModel();
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \BelVG\MeasurementTool\Model\ResourceModel\CustomerElement\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \BelVG\MeasurementTool\Api\Data\CustomerElementInterface::class
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

    public function delete(CustomerElementInterface $element)
    {
        try {
            /** @var CustomerElementModel $elementModel */
            $elementModel = $this->customerElementModelFactory->create();
            $this->resourceCustomerElementModel->load($elementModel, $element->getEntityId());
            $this->resourceCustomerElementModel->delete($elementModel);
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