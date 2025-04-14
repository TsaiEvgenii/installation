<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

namespace BelVG\LayoutCustomizer\Model\Service;

use Magento\Variable\Model\VariableFactory;
use Magento\Variable\Model\ResourceModel\Variable as VariableResource;

/**
 * Class MeasureOldWindowContent
 * If new store is added this service can be used to add new record to 'variable_value' table
 *
 * @package BelVG\LayoutCustomizer\Model\Service
 */
class MeasureOldWindowContent
{
    public const MEASURE_OLD_WINDOW = 'measure_old_window';

    /**
     * @var VariableFactory
     */
    protected VariableFactory $variableFactory;

    /**
     * @var VariableResource
     */
    protected VariableResource $variableResource;

    /**
     * @param VariableFactory $variableFactory
     * @param VariableResource $variableResource
     */
    public function __construct(
        VariableFactory $variableFactory,
        VariableResource $variableResource
    ) {
        $this->variableFactory = $variableFactory;
        $this->variableResource = $variableResource;
    }

    /**
     * @param string[] $data
     * @param int $storeId
     */
    public function saveMeasureOldWindowContent(array $data, int $storeId): void
    {
        $variable = $this->variableFactory->create();
        $variable->loadByCode(self::MEASURE_OLD_WINDOW);
        $variable->setStoreId($storeId);
        $value = $this->getMeasureOldWindowContent($data);
        $variable->setData('html_value', $value);
        $this->variableResource->save($variable);
    }

    /**
     * Default strings in English:
     * $str1 - Enter your frame size
     * $str2 - How to measure
     * $str3 - This drawing is indicative and seen from the outside. All measurements are for frame size.
     * $url - url to page how to measure old frame
     *
     * @param string[] $data
     * @return string
     */
    protected function getMeasureOldWindowContent(array $data): string
    {
        list($str1, $url, $str2, $str3) = $data;
        return '<div class="view-title">
                    <div class="text">
                        <h3>' . $str1 . '</h3>
                        <a href="' . $url . '" class="how-to link" target="_blank">' . $str2 . '</a>
                    </div>
                    <div class="desc">' . $str3 . '</div>
                </div>';
    }
}
