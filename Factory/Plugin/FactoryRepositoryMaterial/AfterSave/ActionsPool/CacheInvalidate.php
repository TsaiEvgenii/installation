<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Plugin\FactoryRepositoryMaterial\AfterSave\ActionsPool;

use BelVG\Factory\Api\Data\FactoryInterface;
use BelVG\Factory\Api\FactoryRepositoryMaterial\AfterSave\ActionInterface;
use Magento\Framework\App\Cache\TypeListInterface;

class CacheInvalidate implements ActionInterface
{
    private TypeListInterface $cacheTypeList;

    public function __construct(
        TypeListInterface $cacheTypeList
    ) {
        $this->cacheTypeList = $cacheTypeList;
    }

    public function execute(FactoryInterface $factory, int $storeId = 0)
    {
        //@todo: check if data has been changed before invalidating
        $this->cacheTypeList->invalidate(\Magento\PageCache\Model\Cache\Type::TYPE_IDENTIFIER);
    }
}
