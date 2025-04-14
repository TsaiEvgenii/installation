<?php

namespace BelVG\LayoutCustomizer\Model\Service;


use Magento\Catalog\Api\ProductRepositoryInterface;

class ConnectLayoutsBySku implements \BelVG\LayoutCustomizer\Api\Service\ConnectLayoutsBySkuInterface
{
    protected $layoutRepository;

    protected $simpleProducts;

    protected $productActionObject;

    protected $productRepository;

    protected $searchCriteria;
    protected $filterGroupBuilder;
    protected $filterBuilder;

    public function __construct(
        \BelVG\LayoutCustomizer\Api\LayoutRepositoryInterface $layoutRepository,
        \BelVG\LayoutCustomizer\Model\Report\SimpleProducts $simpleProducts,
        \Magento\Catalog\Model\Product\Action $productActionObject,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder
    ) {
        $this->layoutRepository = $layoutRepository;
        $this->simpleProducts = $simpleProducts;
        $this->productActionObject = $productActionObject;
        $this->productRepository = $productRepository;
        $this->searchCriteria = $searchCriteria;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @param $identifier
     * @return \BelVG\LayoutCustomizer\Api\Data\LayoutInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLayoutByIdentifier($identifier)
    {
        $filter1 = $this->filterBuilder
            ->setField(\BelVG\LayoutCustomizer\Api\Data\LayoutInterface::IDENTIFIER)
            ->setConditionType('eq')
            ->setValue($identifier)
            ->create();
        $filter_group1 = $this->filterGroupBuilder
            ->addFilter($filter1)
            ->create();
        $this->searchCriteria->setFilterGroups([$filter_group1]);

        $layouts = $this->layoutRepository->getList($this->searchCriteria);
        foreach ($layouts->getItems() as $layout) {
            return $layout;
        }

        return null;
    }

    /**
     * @param string|null $sku
     * @return \Generator
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function assign(string $sku = null)
    {
        if ($sku) {
            $this->simpleProducts->reset();
            $this->simpleProducts->addAttributeToFilter('sku', $sku);
        }

        foreach ($this->simpleProducts as $simpleProduct) {
            $sku = $simpleProduct->getSku();
            /** @var \BelVG\LayoutCustomizer\Api\Data\LayoutInterface $layout */
            $layout = $this->getLayoutByIdentifier($sku);
            if ($layout) {
                foreach ($simpleProduct->getStoreIds() as $store_id) {
                    $this->productActionObject->updateAttributes(
                        [$simpleProduct->getId()],
                        [\BelVG\LayoutCustomizer\Helper\Data::PRODUCT_LAYOUT_ATTR => $layout->getLayoutId()],
                        $store_id
                    );

                    yield [
                        'sku' => $sku,
                        'product_id' => $simpleProduct->getId(),
                        'layout_id' => $layout->getLayoutId(),
                        'layout_identifier' => $layout->getIdentifier(),
                        'store_id' => $store_id,
                        'action' => 'assign'
                    ];
                }
            } else {
                //null old value
                foreach ($simpleProduct->getStoreIds() as $store_id) {
                    $this->productActionObject->updateAttributes(
                        [$simpleProduct->getId()],
                        [\BelVG\LayoutCustomizer\Helper\Data::PRODUCT_LAYOUT_ATTR => NULL],
                        $store_id
                    );
                }
            }
        }
        unset($simpleProduct);
    }
    /**
     * @param string|null $sku
     * @return \Generator
     */
    public function unassign(string $sku = null)
    {
        if ($sku) {
            $this->simpleProducts->addAttributeToFilter('sku', $sku);
        }

        foreach ($this->simpleProducts as $simpleProduct) {
            foreach ($simpleProduct->getStoreIds() as $store_id) {
                $this->productActionObject->updateAttributes(
                    [$simpleProduct->getId()],
                    [\BelVG\LayoutCustomizer\Helper\Data::PRODUCT_LAYOUT_ATTR => NULL],
                    $store_id
                );

                yield [
                    'sku' => $sku,
                    'product_id' => $simpleProduct->getId(),
                    'store_id' => $store_id,
                    'action' => 'unassign'
                ];
            }
        }
        unset($simpleProduct);
    }

    /**
     * @param int $id
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function unassignById(int $id)
    {
        $product = $this->productRepository->getById($id);

        foreach ($product->getStoreIds() as $store_id) {
            $this->productActionObject->updateAttributes(
                [$product->getId()],
                [\BelVG\LayoutCustomizer\Helper\Data::PRODUCT_LAYOUT_ATTR => NULL],
                $store_id
            );
        }
    }
}
