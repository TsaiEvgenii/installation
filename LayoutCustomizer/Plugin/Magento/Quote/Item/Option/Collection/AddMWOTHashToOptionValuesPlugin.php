<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\LayoutCustomizer\Plugin\Magento\Quote\Item\Option\Collection;

use Magento\Quote\Model\ResourceModel\Quote\Item\Option\Collection as Subject;
use Psr\Log\LoggerInterface;

class AddMWOTHashToOptionValuesPlugin
{
    private const LOG_PREFIX = '[BelVG_LayoutCustomizer::AddMWOTHashToOptionValuesPlugin]: ';

    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    /**
     * @param Subject $subject
     * @param bool $printQuery
     * @param bool $logQuery
     * @return array
     */
    public function beforeLoad(
        Subject $subject,
        $printQuery = false,
        $logQuery = false
    ) {
        try {
            if ($subject->isLoaded() !== true) {
                $subject->getSelect()->joinLeft(
                    [
                        'catalog_option_value' => $subject->getTable('catalog_product_option_type_value')
                    ],
                    'catalog_option_value.option_type_id = main_table.value',
                    [
                        'mageworx_optiontemplates_group_option_type_id',
                    ]
                );
            }
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . 'something went wrong: "%s"',
                $t->getMessage()
            ));
        }

        return [$printQuery, $logQuery];
    }
}
