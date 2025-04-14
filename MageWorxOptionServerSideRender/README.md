This module use to render mageworx option on product page. There is no any new functionality, but this module should 
increase perfomance of product page. 

Points to  extend module:
```
\BelVG\MageWorxOptionServerSideRender\Model\Service\CreateBlockService
```

This class response to add new  render design for options. For example you can add new template
``` xml
<virtualType name="specificationDefaultRender" type="BelVG\MageWorxOptionServerSideRender\Model\RenderSpecification">
        <arguments>
            <argument name="additionalRenderAction" xsi:type="object">BelVG\MageWorxOptionServerSideRender\Model\RenderSpecification\AdditionalRenderAction\DummyRenderAction</argument>
            <argument name="blockName" xsi:type="string">\BelVG\MageWorxOptionServerSideRender\Block\Option\Type\Radio</argument>
            <argument name="template" xsi:type="string">BelVG_MageWorxOptionServerSideRender::radio_button_template.phtml</argument>
            <argument name="specification" xsi:type="object">\BelVG\MageWorxOptionServerSideRender\Model\RenderSpecification\TrueSpecification</argument>
        </arguments>
    </virtualType>
```
It is use always TrueSpecification for now. Class \BelVG\MageWorxOptionServerSideRender\Model\Service\CreateBlockService 

use in the next area:
```
\BelVG\MageWorxOptionServerSideRender\Model\Service\ResultRenderPipeline 
```

Also in one type of options we can have different template(select option and special color). This point of extend can be 
here \BelVG\MageWorxOptionServerSideRender\Block\Product\View\Options\Label\RenderSelectList:
``` php
/**
     * @param  $parentBlock
     * @return bool|AbstractBlock
     */
    public function getRender($option, $product, array $data = [])
    {
        $renderName = 'option.select.default';
        $value = $this->getSelectedValue($option, $this->selectedOptions);
        if($value->getData('is_special_color') !== null){
           $renderName = 'option.select.special_color';
        }
        $block = $this->getChildBlock($renderName);
        $block->setData(\array_merge($data, $block->getData()));
        $block->setOption($option);
        $block->setProduct($product);
        $block->setSkipJsReloadPrice(1);
        return $block;
    }

```
This class override main RenderSelectList for adding possibility to choose different templates for same types  of options

\BelVG\MageWorxOptionServerSideRender\Block\Option\Type\AbstractWrapperBlock - class that you should use to extend blocks

This module has information about images of options, price and selected value.

If you want to find selected on product page options please see \BelVG\MageWorxOptionServerSideRender\Model\Context and 
\BelVG\MageWorxOptionServerSideRender\Model\Service\GetSelectedOptions.

Price without discount:
```
\BelVG\MageWorxOptionServerSideRender\Model\Helper\Data
```

Applied discount to price:
```
\BelVG\MageWorxOptionServerSideRender\Model\Service\PriceDiscountService
```

How to disable module:

Disable module in config.php
Return back magepack.config.js




