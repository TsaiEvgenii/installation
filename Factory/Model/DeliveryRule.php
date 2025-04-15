<?php
namespace BelVG\Factory\Model;

use BelVG\Factory\Api\Data\DeliveryRuleInterface;
use BelVG\Factory\Api\Data\DeliveryRuleInterfaceFactory as DataModelFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model;
use Magento\Framework\Registry;

class DeliveryRule extends Model\AbstractModel
{
    protected $objectHelper;
    protected $dataModelFactory;

    public function __construct(
        Model\Context $context,
        Registry $registry,
        DataObjectHelper $objectHelper,
        DataModelFactory $dataModelFactory,
        array $data = [])
    {
        parent::__construct($context, $registry, null, null);
        $this->objectHelper = $objectHelper;
        $this->dataModelFactory = $dataModelFactory;
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\DeliveryRule::class);
    }

    public function getDataModel()
    {
        $dataModel = $this->dataModelFactory->create();
        $this->objectHelper->populateWithArray(
            $dataModel,
            $this->getData(),
            DeliveryRuleInterface::class);
        return $dataModel;
    }
}
