<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Plugin\Block\Product;

use BelVG\RaptorSmartAdvisor\Block\Product as Subject;
use BelVG\MageWorxOptionServerSideRender\Model\Helper\Data as MWOTServerSideRenderHelper;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Psr\Log\LoggerInterface;

class UseMWOTPricePlugin
{
    private const LOG_PREFIX = '[BelVG_MageWorxOptionServerSideRender::UseMWOTPricePlugin]: ';

    private MWOTServerSideRenderHelper $mwotSSRHelper;
    private TemplateContext $templateContext;
    private LoggerInterface $logger;

    public function __construct(
        MWOTServerSideRenderHelper $mwotSSRHelper,
        TemplateContext $templateContext,
        LoggerInterface $logger
    ) {
        $this->mwotSSRHelper = $mwotSSRHelper;
        $this->templateContext = $templateContext;
        $this->logger = $logger;
    }

    /**
     * @param Subject $subject
     * @param $result
     * @return mixed|string|null
     */
    public function afterGetPrice(
        Subject $subject,
        $result
    ) {
        try {
            $product = $subject->getProduct();
            if (!$product) {
                return null;
            }

            $layoutData = $this->prepareLayoutData();
            $price = $this->mwotSSRHelper->getOptionsPriceWithDiscount(
                $product,
                $this->templateContext,
                $layoutData
            );

            $result = $subject->formatPrice((float)$price);
        } catch (\Throwable $t) {
            $this->logger->error(
                sprintf(
                    self::LOG_PREFIX . 'error message: "%s".',
                    $t->getMessage()
                )
            );
        }

        return $result;
    }

    /**
     * According to Thomas it should be always default width and height
     *
     * @return int[]
     */
    private function prepareLayoutData() :array
    {
        return [
            'width' => 0,
            'height' => 0
        ];
    }
}
