<?php
namespace BelVG\Factory\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class FactoryActions extends Column
{
    protected $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [])
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $storeId = $this->context->getRequestParam('store');
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->getEditUrl($storeId, $item),
                    'label' => __('Edit'),
                    'hidden' => false
                ];
            }
        }
        return $dataSource;
    }

    protected function getEditUrl($storeId, array $item)
    {
        // assert(isset($item['factory_id']));
        return $this->urlBuilder->getUrl('factory/factory/edit', [
            'factory_id' => $item['factory_id'],
            'store'      => $storeId
        ]);
    }
}
