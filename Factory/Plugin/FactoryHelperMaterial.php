<?php
namespace BelVG\Factory\Plugin;

use BelVG\Factory\Api\Data\FactoryMaterialInterface;
use BelVG\Factory\Api\Data\FactoryMaterialInterfaceFactory;
use BelVG\Factory\Api\Data\FactoryInterface;
use Belvg\Factory\Controller\Adminhtml\Factory\Helper\Factory as Subject;
use Magento\Framework\Api\DataObjectHelper;

class FactoryHelperMaterial
{
    protected $factoryMaterialFactory;
    protected $objectHelper;

    public function __construct(
        FactoryMaterialInterfaceFactory $factoryMaterialFactory,
        DataObjectHelper $objectHelper)
    {
        $this->factoryMaterialFactory = $factoryMaterialFactory;
        $this->objectHelper = $objectHelper;
    }

    public function beforeSaveObject(
        Subject $helper,
        FactoryInterface $factory,
        array $data)
    {
        if (!isset($data['materials']))
            $data['materials'] = [];

        if (!is_array($data['materials']))
            return;

        // Create materials
        $materials = array_map([$this, 'createFactoryMaterial'], $data['materials']);

        // Set factory materials
        $extensionAttributes = $factory->getExtensionAttributes();
        $extensionAttributes->setMaterials($materials);
        $factory->setExtensionAttributes($extensionAttributes);
    }

    protected function createFactoryMaterial(array $data)
    {
        $factoryMaterial = $this->factoryMaterialFactory->create();
        $this->objectHelper->populateWithArray(
            $factoryMaterial,
            array_merge(
                ['delivery_rules' => []],
                $data),
            FactoryMaterialInterface::class);
        return $factoryMaterial;
    }
}
