<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\Factory\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

class EstimationType extends Column
{
    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {

                $columnName = $this->getData('name');
                $options = $this->getData('options');
                if (!empty($options[$item[$columnName]])) {

                    $item[$columnName] = $options[$item[$columnName]]['label'];
                }
            }
        }
        return $dataSource;
    }
}
