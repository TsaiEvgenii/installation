<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\MeasurementTool\Block\MeasurementTool;


use BelVG\MeasurementTool\Api\Data\MeasurementToolInterface;
use BelVG\MeasurementTool\Api\MeasurementToolRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Grid extends Template
{
    private const LOG_PREFIX = '[BelVG_MeasurementTool::GridBlock]: ';
    protected $_template = 'BelVG_MeasurementTool::measurement_tool/grid.phtml';

    public function __construct(
        protected FilterBuilder $filterBuilder,
        protected SortOrderBuilder $sortOrderBuilder,
        protected FilterGroupBuilder $filterGroupBuilder,
        protected SearchCriteriaBuilder $searchCriteriaBuilder,
        protected MeasurementToolRepositoryInterface $measurementToolRepository,
        protected StoreManagerInterface $storeManager,
        protected Session $customerSession,
        protected LoggerInterface $logger,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _construct(): void
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Measurement Tools'));
    }

    public function getMeasurementTools(): array
    {
        try {
            $customerId = (int)$this->customerSession->getCustomerId();
            $filterCustomerId = $this->filterBuilder
                ->setField(MeasurementToolInterface::CUSTOMER_ID)
                ->setValue($customerId)
                ->setConditionType('eq')
                ->create();

            $sortOrder = $this->sortOrderBuilder
                ->setField('created_at')
                ->setDirection(SortOrder::SORT_DESC)
                ->create();

            $filterGroupCustomerId = $this->filterGroupBuilder->addFilter($filterCustomerId)->create();
            $criteria = $this->searchCriteriaBuilder
                ->setFilterGroups([$filterGroupCustomerId])
                ->addSortOrder($sortOrder)
                ->create();
            $measurementToolList = $this->measurementToolRepository->getList($criteria);

            return $measurementToolList->getItems();
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
            return [];
        }
    }

    public function getElementQty(MeasurementToolInterface $measurementTool): ?int
    {
        $elemsQty = 0;
        $rooms = $measurementTool->getRooms();
        foreach ($rooms as $room) {
            $elements = $room->getElements();
            //Todo: number of elements or qty of every element
            $elemsQty += count($elements);
//            foreach ($elements as $element) {
//                $elemsQty += ($element['qty'] ?? 0);
//            }
        }

        return $elemsQty;
    }

    public function getEmptyMeasurementToolMessage(): \Magento\Framework\Phrase
    {
        return __('There are no saved measurement tools yet.');
    }

    public function getViewUrl(MeasurementToolInterface $measurementTool = null, $storeId = null): string
    {
        try {
            if ($measurementTool === null) {
                return $this->storeManager->getStore($storeId)->getUrl('measurement_tool');
            }
            return $this->storeManager->getStore($storeId)->getUrl('*/index/index', ['measurement_tool_id' => $measurementTool->getEntityId()]);
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
            return '';
        }

    }

    //Todo: add build logic to add element to the cart
    public function getBuildUrl(MeasurementToolInterface $measurementTool, $storeId = null): string
    {
        try {
            return $this->storeManager->getStore($storeId)->getUrl('*/index/build', ['measurement_tool_id' => $measurementTool->getEntityId()]);
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
            return '';
        }

    }

}