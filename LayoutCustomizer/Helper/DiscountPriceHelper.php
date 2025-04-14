<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\LayoutCustomizer\Helper;

use BelVG\LayoutCustomizer\Block\Cart\DiscountPrice;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;

class DiscountPriceHelper extends AbstractHelper
{
    private PageFactory $resultPageFactory;
    private $discountBlock = null;

    public function __construct(
        PageFactory $resultPageFactory,
        Context $context
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    /**
     * @param CartItemInterface $item
     * @param array $data
     * @return string
     */
    public function formatDiscountPrice(
        CartItemInterface $item,
        array $data = []
    ) {
        $data = array_merge($data, [
            'item' => $item
        ]);

        $block = $this->getDiscountPriceBlock();
        if ($block) {
            $block->addData($data);

            return $block->toHtml();
        }

        return '';
    }

    private function getDiscountPriceBlock() {
        if (!$this->discountBlock) {
            $resultPage = $this->resultPageFactory->create();
            $this->discountBlock = $resultPage->getLayout()->createBlock(DiscountPrice::class);
        }

        return $this->discountBlock;
    }
}
