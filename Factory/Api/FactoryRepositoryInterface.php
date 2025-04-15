<?php
namespace BelVG\Factory\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

use BelVG\Factory\Api\Data\FactoryInterface;

interface FactoryRepositoryInterface
{
    public function getById($factoryId, $storeId = null);

    public function getList(SearchCriteriaInterface $criteria);

    public function save(Data\FactoryInterface $factory, $storeId = null);

    public function delete(Data\FactoryInterface $factory);

    public function deleteById($factoryId);
}
