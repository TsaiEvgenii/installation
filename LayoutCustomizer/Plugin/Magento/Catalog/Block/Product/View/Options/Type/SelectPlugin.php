<?php


namespace BelVG\LayoutCustomizer\Plugin\Magento\Catalog\Block\Product\View\Options\Type;

use \Magento\Catalog\Block\Product\View\Options\Type\Select;
use \BelVG\Stdlib\src\StringWrapper\MbString;
use MageWorx\OptionBase\Model\Product\Option\Value\AdditionalHtmlData;
use \BelVG\LayoutOptionPriceType\Plugin\Magento\Catalog\Model\Config\Source\Product\Options\PricePlugin as LayoutOptionPriceTypePricePlugin;
use BelVG\MageWorxSpecialColor\Model\SpecialColorMageWorxOption;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

class SelectPlugin
{
    private const LOG_PREFIX = '[BelVG_LayoutCustomizer::SelectPlugin]: ';

    protected MbString $mbString;
    protected AdditionalHtmlData $additionalHtmlData;
    protected UrlInterface $urlInterface;
    protected LoggerInterface $logger;

    public function __construct(
        MbString $mbString,
        AdditionalHtmlData $additionalHtmlData,
        UrlInterface $urlInterface,
        LoggerInterface $logger
    ) {
        $this->mbString = $mbString;
        $this->additionalHtmlData = $additionalHtmlData;
        $this->urlInterface = $urlInterface;
        $this->logger = $logger;
    }

    /**
     * @param Select $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundGetValuesHtml(Select $subject, \Closure $proceed)
    {
        $result = $proceed();

        try {
            $option = $subject->getOption();

            $dom = new \DOMDocument();
            $dom->preserveWhiteSpace = false;

//            $this->mbString->setEncoding('UTF-8', 'html-entities');
            $result = $this->mbString->convert($result);

            libxml_use_internal_errors(true);
            $dom->loadHTML($result);
            libxml_clear_errors();

            foreach ($this->additionalHtmlData->getData() as $additionalHtmlItem) {
                $additionalHtmlItem->getAdditionalHtml($dom, $option);
            }

            $xpath = new \DOMXPath($dom);

            $count = 1;
            /** @var \Magento\Catalog\Model\Product\Option\Value $value */
            foreach ($option->getValues() as $value) {
                $count++;

                $select =
                    $xpath->query('//option[@value="'.$value->getId().'"]')->item(0);

                $input =
                    $xpath->query('//div/div/div[descendant::label[@for="options_'.$option->getId().'_'.$count.'"]]')->item(0);

                $element = $select ? $select : $input;

                $this->checkMWOTRefs($value);

                if ($element) {
                    $element->setAttribute("option_type_id", $value->getData('mageworx_option_type_id'));
                    $element->setAttribute("mageworx_group_option_type_id", $value->getData('mageworx_optiontemplates_group_option_type_id')); //override reason
                    $element->setAttribute("group_option_value_id", $value->getData('group_option_value_id')); //override reason
                    $this->appendAttributeToInputElement($value, $element);

                    if ($value->getPriceType() == 'percent') {
                        $element->setAttribute("percentInfo", $value->getPrice(false)); //override reason
                    } elseif ($value->getPriceType() == LayoutOptionPriceTypePricePlugin::VALUE_SQM_PRICE) {
                        $element->setAttribute("sqmPrice", $value->getPrice(false)); //override reason
                    }

                    if (!empty($option->getData('inside_outside_color'))) {
                        $element->setAttribute("inoutColor", $option->getData('inside_outside_color')); //override reason
                    }
                    $element->setAttribute("is_default", $value->getData('is_default')); //override reason
                }
            }

            $resultBody = $dom->getElementsByTagName('body')->item(0);
            $result = $this->getInnerHtml($resultBody);
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . 'page url: "%s", something went wrong: "%s"',
                $this->urlInterface->getCurrentUrl(),
                $t->getMessage()
            ));
        }

        return $result;
    }

    /**
     * @param \DOMElement $node
     * @return string
     */
    protected function getInnerHtml(\DOMElement $node)
    {
        $innerHTML= '';
        $children = $node->childNodes;
        foreach ($children as $child) {
            $innerHTML .= $child->ownerDocument->saveXML($child);
        }

        return $innerHTML;
    }

    /**
     * Adds 'mageworx_optiontemplates_group_option_type_id' attribute to input element
     *
     * @param $value
     * @param $element
     */
    protected function appendAttributeToInputElement($value, $element): void
    {
        $value = !empty($value->getDataByKey(SpecialColorMageWorxOption::MAGEWORX_OPTION_TEMPLATE_GROUP_OPTION_TYPE_ID)) ?
            $value->getDataByKey(SpecialColorMageWorxOption::MAGEWORX_OPTION_TEMPLATE_GROUP_OPTION_TYPE_ID) : '';
        $element->getElementsByTagName('input')[0]->setAttribute(
            SpecialColorMageWorxOption::MAGEWORX_OPTION_TEMPLATE_GROUP_OPTION_TYPE_ID,
            $value
        );
    }

    private function checkMWOTRefs(\Magento\Catalog\Model\Product\Option\Value $value) {
        if (empty($value->getData('mageworx_option_type_id'))) {
            $this->logEmptyMWOTRefs($value, __('"mageworx_option_type_id" is empty')->getText());
        }

        if (empty($value->getData('mageworx_optiontemplates_group_option_type_id'))) {
            $this->logEmptyMWOTRefs($value, __('"mageworx_optiontemplates_group_option_type_id" is empty')->getText());
        }
    }

    private function logEmptyMWOTRefs(
        \Magento\Catalog\Model\Product\Option\Value $value,
        string $msg
    ) {
        $this->logger->error(sprintf(
            self::LOG_PREFIX . '"%s; page url: "%s", title "%s", option_type_id "%s", option_id "%s"',
            $msg,
            $this->urlInterface->getCurrentUrl(),
            $value->getData('title'),
            $value->getData('option_type_id'),
            $value->getData('option_id')
        ));
    }
}
