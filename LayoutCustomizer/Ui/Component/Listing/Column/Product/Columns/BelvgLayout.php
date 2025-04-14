<?php


namespace BelVG\LayoutCustomizer\Ui\Component\Listing\Column\Product\Columns;

use \BelVG\LayoutCustomizer\Helper\Data as LayoutHelper;


class BelvgLayout extends \Magento\Ui\Component\Listing\Columns\Column
{
    public $layoutRepository;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \BelVG\LayoutCustomizer\Api\LayoutRepositoryInterface $layoutRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->layoutRepository = $layoutRepository;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[LayoutHelper::PRODUCT_LAYOUT_ATTR])) {
                    $layout = $this->layoutRepository->getById($item[LayoutHelper::PRODUCT_LAYOUT_ATTR]);

                    $item[$fieldName] = $layout->getIdentifier();
                }
            }
        }

        return $dataSource;
    }
}
