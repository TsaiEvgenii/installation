<?php
namespace BelVG\LayoutCustomizer\Model\Service;

use BelVG\LayoutCustomizer\Api\Data\LayoutInterface;
use BelVG\LayoutCustomizer\Api\Data\LayoutInterfaceFactory;
use BelVG\LayoutCustomizer\Api\LayoutRepositoryInterface;
use BelVG\LayoutCustomizer\Api\Service\DuplicateLayoutDataInterface;
use BelVG\LayoutCustomizer\Helper\Layout\Block as BlockHelper;
use BelVG\LayoutCustomizer\Model\LayoutStoreFactory;
use BelVG\LayoutCustomizer\Model\ResourceModel\LayoutStore\CollectionFactory
    as LayoutStoreCollectionFactory;

class DuplicateLayout implements DuplicateLayoutDataInterface
{
    protected $layoutRepo;
    protected $layoutFactory;
    protected $layoutStoreFactory;
    protected $layoutStoreCollectionFactory;
    protected $blockHelper;
    protected $resource;
    protected $customizerConfig;
    protected $storeFieldsService;

    public function __construct(
        LayoutRepositoryInterface $layoutRepo,
        LayoutInterfaceFactory $layoutFactory,
        LayoutStoreFactory $layoutStoreFactory,
        LayoutStoreCollectionFactory $layoutStoreCollectionFactory,
        BlockHelper $blockHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        \BelVG\LayoutCustomizer\Model\Config $layoutCustomizerConfig,
        \BelVG\LayoutCustomizer\Api\Service\StoreFieldsInterface $storeFields
    ) {
        $this->layoutRepo = $layoutRepo;
        $this->layoutFactory = $layoutFactory;
        $this->layoutStoreFactory = $layoutStoreFactory;
        $this->layoutStoreCollectionFactory = $layoutStoreCollectionFactory;
        $this->blockHelper = $blockHelper;
        $this->resource = $resource;
        $this->customizerConfig = $layoutCustomizerConfig;
        $this->storeFieldsService = $storeFields;
    }

    /**
     * @param LayoutInterface $layout
     * @return mixed
     */
    protected function createCopy(LayoutInterface $layout)
    {
        return $this->layoutFactory
            ->create()
            ->setIdentifier($layout->getIdentifier() . '-duplicate')
            ->setHeight($layout->getHeight())
            ->setWidth($layout->getWidth())
            ->setIsActive(false)
            ->setHorizontalFrame($layout->getHorizontalFrame())
            ->setVerticalFrame($layout->getVerticalFrame())
            ->setSqmLevelStep2($layout->getSqmLevelStep2())
            ->setBasePrice($layout->getBasePrice())
            ->setSqmPrice($layout->getSqmPrice())
            ->setSqmPriceStep2($layout->getSqmPriceStep2())
            ->setFamilyId($layout->getFamilyId())
            ->setLayoutmaterialId($layout->getLayoutmaterialId())
            ->setInoutcolorPriceBothDiff($layout->getInoutcolorPriceBothDiff())
            ->setInoutcolorPriceBothSame($layout->getInoutcolorPriceBothSame())
            ->setInoutcolorPriceInsideOtherwhite($layout->getInoutcolorPriceInsideOtherwhite())
            ->setInoutcolorPriceOutsideOtherwhite($layout->getInoutcolorPriceOutsideOtherwhite());
    }

    /**
     * @param LayoutInterface $layout
     * @param callable|null $afterCopy
     * @return LayoutInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function copyAndSave(LayoutInterface $layout, callable $afterCopy = null): LayoutInterface
    {
        try {
            // Start transaction
            $this->resource->getConnection()->beginTransaction();

            // Copy data
            $copy = $this->createCopy($layout);

            if (!is_null($afterCopy)) {
                $afterCopy($copy);
            }

            $existsCopy = $this->isExistent($copy->getIdentifier());
            if (!$existsCopy?->getIdentifier()) {
                $copy = $this->layoutRepo->save($copy, 0);

                // Copy store data
                $layoutStoreCollection = $this->layoutStoreCollectionFactory->create()
                    ->addLayoutFilter($layout->getLayoutId())
                    ->addFieldToFilter('store_id', ['neq' => 0]);
                foreach ($layoutStoreCollection as $layoutStore) {
                    $storeCopy = $this->layoutStoreFactory->create()
                        ->setData($layoutStore->getData())
                        ->setId(null)
                        ->setLayoutId($copy->getLayoutId());
                    $storeCopy->save();
                }

                // Copy blocks
                $blockData = $this->blockHelper->load($layout->getLayoutId());
                $this->blockHelper->save($copy->getLayoutId(), $this->blockHelper->stripIds($blockData));


                // Commit transaction
            } else {
                $copy = $existsCopy;
            }
            $this->resource->getConnection()->commit();
        } catch (\Exception $e) {
            // Rollback transaction
            $this->resource->getConnection()->rollBack();

            throw $e;
        }

        return $copy;
    }

    /**
     * @param string|null $identifier
     * @return LayoutInterface|null
     */
    private function isExistent(?string $identifier): ?LayoutInterface
    {
        try {
            $layout = $this->layoutRepo->getByIdentifier($identifier);
        } catch (\Exception) {
            $layout = null;
        }

        return $layout;
    }
}
