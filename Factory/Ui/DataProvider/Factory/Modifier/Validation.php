<?php
namespace BelVG\Factory\Ui\DataProvider\Factory\Modifier;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Action;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class Validation implements ModifierInterface
{
    protected $request;
    protected $urlBuilder;

    public function __construct(
        RequestInterface $request,
        UrlInterface $urlBuilder)
    {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
    }

    public function modifyData(array $data)
    {
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        $meta['listing_top']['children']['listing_massaction']['children']['validate'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Action::NAME,
                        'label'         => __('Validate'),
                        'type'          => 'validate',
                        'url'           => $this->getValidateUrl()
                    ]]]];

        return $meta;
    }

    protected function getValidateUrl()
    {
        return $this->urlBuilder->getUrl(
            '*/*/massValidate',
            ['store' => $this->request->getParam('store')]);
    }
}
