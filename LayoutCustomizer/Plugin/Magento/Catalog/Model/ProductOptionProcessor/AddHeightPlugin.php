<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\LayoutCustomizer\Plugin\Magento\Catalog\Model\ProductOptionProcessor;

use BelVG\LayoutCustomizer\Helper\Data as LayoutCustomizerHelper;
use Magento\Catalog\Model\CustomOptions\CustomOption;
use Magento\Catalog\Model\CustomOptions\CustomOptionFactory;
use Magento\Catalog\Model\ProductOptionProcessor as Subject;
use Magento\Framework\DataObject;
use Psr\Log\LoggerInterface;

class AddHeightPlugin
{
    private const LOG_PREFIX = '[BelVG_LayoutCustomizer::ProductOptionProcessor_AddHeightPlugin]: ';

    private CustomOptionFactory $customOptionFactory;
    private LoggerInterface $logger;

    public function __construct(
        CustomOptionFactory $customOptionFactory,
        LoggerInterface $logger
    ) {
        $this->customOptionFactory = $customOptionFactory;
        $this->logger = $logger;
    }

    /**
     * Build API for M2 to be able to read/write checkpoint history and other order details
     * https://youtrack.belvgdev.com/issue/SD-2832
     *
     * @param Subject $subject
     * @param $result
     * @param DataObject $request
     * @return array
     */
    public function afterConvertToProductOption(
        Subject $subject,
                $result,
        DataObject $request
    ) {
        try {
            $requestArray = $request->toArray();
            $layoutHeight = $requestArray['product_options']['info_buyRequest'][LayoutCustomizerHelper::PRODUCT_HEIGHT_OPTION_KEY] ?? [];
            if ($layoutHeight) {
                /** @var CustomOption $option */
                $option = $this->customOptionFactory->create();
                $option
                    ->setOptionId(LayoutCustomizerHelper::PRODUCT_HEIGHT_OPTION_KEY)
                    ->setOptionValue($layoutHeight);
                $result['custom_options'][] = $option;
            }
        } catch (\Throwable $t) {
            $this->logger->error(self::LOG_PREFIX . $t->getMessage());
        }

        return $result;
    }
}
