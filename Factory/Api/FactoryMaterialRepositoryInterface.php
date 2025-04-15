<?php
namespace BelVG\Factory\Api;

use BelVG\Factory\Api\Data\FactoryInterface;
use BelVG\Factory\Api\Data\FactoryMaterialInterface;

interface FactoryMaterialRepositoryInterface
{
    public function getById($factoryMaterialId);

    public function getListByFactory(FactoryInterface $factory,  bool $checkActivity);

    public function save(FactoryMaterialInterface $factoryMaterial);

    public function delete(FactoryMaterialInterface $factoryMaterial);
    public function deleteById($factoryMaterialId);
}
