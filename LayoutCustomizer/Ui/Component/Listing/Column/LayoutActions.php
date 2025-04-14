<?php


namespace BelVG\LayoutCustomizer\Ui\Component\Listing\Column;

class LayoutActions extends \Magento\Ui\Component\Listing\Columns\Column
{

    const URL_PATH_DETAILS = 'belvg_layoutcustomizer/layout/details';
    protected $urlBuilder;
    const URL_PATH_EDIT = 'belvg_layoutcustomizer/layout/edit';
    const URL_PATH_DELETE = 'belvg_layoutcustomizer/layout/delete';
    const URL_PATH_DUPLICATE = 'belvg_layoutcustomizer/layout/duplicate';

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $storeId = $this->context->getRequestParam('store');
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['layout_id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'layout_id' => $item['layout_id'],
                                    'store'     => $storeId
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'layout_id' => $item['layout_id'],
                                    'store'     => $storeId
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete "${ $.$data.identifier }"'),
                                'message' => __('Are you sure you wan\'t to delete a "${ $.$data.identifier }" record?')
                            ]
                        ],
                        'duplicate' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DUPLICATE,
                                [
                                    'layout_id' => $item['layout_id'],
                                    'store'     => $storeId
                                ]
                            ),
                            'label' => __('Duplicate')
                        ],
                        'copy' => [
                            'label' => __('Copy...'),
                            'callback' => [
                                'provider' => 'belvg_layoutcustomizer_layout_listing.belvg_layoutcustomizer_layout_listing.material_select',
                                'target'   => 'openModal',
                                'params'   => [
                                    'layout_id' => $item['layout_id']
                                ]
                            ]
                        ],
                        'copy_blocks' => [
                            'label' => __('Copy drawing...'),
                            'callback' => [
                                'provider' => 'belvg_layoutcustomizer_layout_listing.belvg_layoutcustomizer_layout_listing.layout_select',
                                'target'   => 'openModal',
                                'params'   => [
                                    'layout_id' => $item['layout_id']
                                ]
                            ]
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
