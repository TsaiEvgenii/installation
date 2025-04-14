<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\LayoutCustomizer\Plugin\Block\SaveCart\View\Cart\Item\Renderer;


use MageKey\SaveCart\Block\SaveCart\View\Cart\Item\Renderer;
use Psr\Log\LoggerInterface;

class AddMWOTHashToCartItemRenderer
{

    private const LOG_PREFIX = '[BelVG_LayoutCustomizer::AddMWOTHashToCartItemRendererPlugin]: ';

    public function __construct(
        protected LoggerInterface $logger
    ) {
    }

    public function afterGetItemOptions(
        Renderer $subject,
        $result
    ) {
        try {
            $item = $subject->getItem();
            $product = $item->getProduct();
            $optionIds = $item->getOptionByCode('option_ids');
            if ($optionIds && $optionIds->getValue()) {
                foreach (explode(',', $optionIds->getValue()) as $optionId) {
                    $option = $product->getOptionById($optionId);
                    if ($option) {
                        $customItemOptions = $this->getPreparedCustomOptions($item);
                        $itemOption = $customItemOptions[$option->getId()] ?? null;
                        $foundKey = array_search($option->getId(), array_column($result, 'option_id'));
                        if ($itemOption !== null && isset($result[$foundKey])) {
                            $result[$foundKey]['mageworx_optiontemplates_group_option_type_id']
                                = $itemOption['mageworx_optiontemplates_group_option_type_id'];
                        }
                    }
                }
            }
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }

        return $result;
    }

    protected function getPreparedCustomOptions($item): array
    {
        $preparedCustomOptions = [];
        $customOptions = $item->getCustomOptions();
        foreach ($customOptions as $customOption) {
            $preparedCustomOptions[$customOption['option_id']] = $customOption;
        }

        return $preparedCustomOptions;
    }
}