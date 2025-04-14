<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\LayoutCustomizer\Plugin\Magento\Catalog\Helper;

use Magento\Catalog\Helper\Product\Configuration as Subject;
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Psr\Log\LoggerInterface;

class AddMWOTHashToProductHelperConfiguratorPlugin
{
    private const LOG_PREFIX = '[BelVG_LayoutCustomizer::AddMWOTHashToProductHelperConfiguratorPlugin]: ';

    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    /**
     * Plugin reason: adding the MWOT hash
     *
     * @param Subject $subject
     * @param $result
     * @param ItemInterface $item
     * @return mixed
     */
    public function afterGetCustomOptions(
        Subject $subject,
        $result,
        ItemInterface $item
    ) {
        try {
            $product = $item->getProduct();
            $optionIds = $item->getOptionByCode('option_ids');
            if ($optionIds && $optionIds->getValue()) {
                foreach (explode(',', $optionIds->getValue()) as $optionId) {
                    $option = $product->getOptionById($optionId);
                    if ($option) {
                        $itemOption = $item->getOptionByCode('option_' . $option->getId());
                        //plugin reason: adding the MWOT hash
                        $foundKey = array_search($option->getId(), array_column($result, 'option_id'));
                        if (isset($result[$foundKey])) {
                            $result[$foundKey]['mageworx_optiontemplates_group_option_type_id'] = $itemOption->getData('mageworx_optiontemplates_group_option_type_id');
                        }
                    }
                }
            }
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . 'something went wrong: "%s"',
                $t->getMessage()
            ));
        }

        return $result;
    }
}
