<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */
declare(strict_types=1);


namespace BelVG\InstallationElements\Block\Cart;


use BelVG\InstallationElements\Block\Cart\Installation\LayoutProcessorInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Psr\Log\LoggerInterface;

class Installation extends Template
{
    private const LOG_PREFIX = '[BelVG_InstallationElements::InstallationBlock]: ';
    /**
     * @var LayoutProcessorInterface[]
     */
    protected array $layoutProcessors;

    public function __construct(
        private LoggerInterface $logger,
        Context $context,
        array $data = [],
        array $layoutProcessors = [],
    ) {
        $this->layoutProcessors = $layoutProcessors;
        parent::__construct($context, $data);
    }

    public function getJsLayout(): false|string
    {
        try {
            foreach ($this->layoutProcessors as $processor) {
                $this->jsLayout = $processor->process($this->jsLayout);
            }
        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }
        return json_encode($this->jsLayout);
    }
}