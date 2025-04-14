<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\LayoutCustomizer\Model;

use BelVG\LayoutCustomizer\Api\BulkLayoutRepositoryInterface;
use BelVG\LayoutCustomizer\Api\Data\LayoutInterface;
use BelVG\LayoutCustomizer\Api\Data\LayoutSearchResultsInterfaceFactory;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Collection as LayoutCollection;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\CollectionFactory as LayoutCollectionFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

class BulkLayoutRepository implements BulkLayoutRepositoryInterface
{
    public function __construct(
        private LayoutCollectionFactory $layoutCollectionFactory,
        private JoinProcessorInterface $extensionAttributesJoinProcessor,
        private CollectionProcessorInterface $collectionProcessor,
        private LayoutSearchResultsInterfaceFactory $searchResultsFactory,
        private EventManager $eventManager
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        SearchCriteriaInterface $criteria,
        string $storeId
    ) {
        /** @var LayoutCollection $collection */
        $collection = $this->layoutCollectionFactory->create();
        $collection->setStoreId($storeId);
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            LayoutInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $this->eventManager->dispatch('belvg_layout_customizer_get_list_collection_modify', [
            'collection' => &$collection,
            'storeId' => $storeId
        ]);

        $items = [];
        /** @var BelVG\LayoutCustomizer\Model\Layout $model */
        foreach ($collection as $model) {
            $dataModel = $model->getDataModel();
            $this->eventManager->dispatch('belvg_layout_customizer_get_list_model_modify', [
                'dataModel' => &$dataModel,
                'model' => $model,
                'storeId' => $storeId
            ]);

            $items[] = $dataModel;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    public function getUpdatedList(
        string $datetime,
        string $storeId
    ) {
        // TODO: Implement getUpdatedList() method.
    }

    public function saveList(
        string $data,
        string $storeId
    ) {
        // TODO: Implement save() method.
    }
}
