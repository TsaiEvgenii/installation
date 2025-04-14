<?php


namespace BelVG\LayoutCustomizer\Model\Config\Source\Layout;


class Options extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected $collection = null;

    public $request;

    public $layoutFactory;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \BelVG\LayoutCustomizer\Model\LayoutFactory  $layoutFactory
    )
    {
        $this->request = $request;
        $this->layoutFactory = $layoutFactory;
    }

    public function getCollection()
    {
        if ($this->collection === NULL) {
            $layout = $this->layoutFactory->create();
            $this->collection = $layout->getCollection();
        }

        return $this->collection;
    }

    public function getAllOptions()
    {
        $this->getCollection();

        $result = [];
        $result[] = [
            'value' => 0,
            'label' => '---'
        ];
        foreach ($this->collection as $layout) {
            $result[] = [
                'value' => $layout->getLayoutId(),
                'label' => $layout->getIdentifier()
            ];
        }
        unset($option);

        return $result;
    }
}