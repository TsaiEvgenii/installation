<?php
namespace BelVG\Factory\Model;

use BelVG\Factory\Api\Data\FactoryInterface;
use BelVG\Factory\Api\Data\FactoryInterfaceFactory as DataModelFactory;
use BelVG\Factory\Model\Service\Source\CalculationTypes;
use BelVG\OrderFactory\Model\Service\DateTime as DateTimeService;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model;
use Magento\Framework\Registry;

class Factory extends Model\AbstractModel
{
    use DefaultStoreId;

    protected $objectHelper;
    protected $dataModelFactory;

    public function __construct(
        Model\Context $context,
        Registry $registry,
        DataObjectHelper $objectHelper,
        DataModelFactory $dataModelFactory,
        protected readonly DateTimeService $dateTimeService,
        array $data = [])
    {
        parent::__construct($context, $registry, null, null, $data);
        $this->objectHelper = $objectHelper;
        $this->dataModelFactory = $dataModelFactory;
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\Factory::class);
    }

    public function getDataModel()
    {
        $dataModel = $this->dataModelFactory->create();
        $this->objectHelper->populateWithArray(
            $dataModel,
            $this->getData(),
            FactoryInterface::class);
        return $dataModel;
    }

    /**
     * @param FactoryInterface $dataModel
     * @return int
     */
    public function getDefaultDeliveryTimeByCalculationType(FactoryInterface $dataModel): int
    {
        $time = (int) $dataModel->getDefaultDeliveryTime();
        if (CalculationTypes::tryFrom((int)$dataModel->getCalculationType()) === CalculationTypes::STATIC) {

            $today = new \DateTime();
            $currentWeek = (int) $today->format('W');
            $lastWeek = $this->dateTimeService->getLastIsoWeekForDate($today);

            $time = $time < $currentWeek ? $lastWeek + $time - $currentWeek : $time - $currentWeek;
        }

        return $time;
    }
}
