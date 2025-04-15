<?php
namespace BelVG\Factory\Model;

use BelVG\Factory\Api\Data\FactoryMaterialInterface;
use BelVG\Factory\Api\Data\FactoryMaterialInterfaceFactory as DataModelFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model;
use Magento\Framework\Registry;

class FactoryMaterial extends Model\AbstractModel
{
    use DefaultStoreId;

    protected $objectHelper;
    protected $dataModelFactory;

    public function __construct(
        Model\Context $context,
        Registry $registry,
        DataObjectHelper $objectHelper,
        DataModelFactory $dataModelFactory,
        array $data = [])
    {
        parent::__construct($context, $registry, null, null, $data);
        $this->objectHelper = $objectHelper;
        $this->dataModelFactory = $dataModelFactory;
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\FactoryMaterial::class);
    }

    public function getDataModel()
    {
        $dataModel = $this->dataModelFactory->create();
        $this->objectHelper->populateWithArray(
            $dataModel,
            $this->getData(),
            FactoryMaterialInterface::class);

        return $dataModel;
    }
}
