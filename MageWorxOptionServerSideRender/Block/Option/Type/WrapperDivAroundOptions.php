<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Block\Option\Type;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class WrapperDivAroundOptions extends AbstractWrapperBlock implements ArgumentInterface
{
    protected $_template = "BelVG_MageWorxOptionServerSideRender::wrapper_around_template.phtml";

    public function process(string $result): string
    {
        $divs = $this->parser->get($result);
        $firstDiv = true;
        foreach ($divs as $div) {
            if ($firstDiv) {
                $node = $this->parser->createFragment();
                $this->setValueId($this->getDefaultValueId());
                $node->appendXML($this->toHtml());
                $label = $div->parentNode->getElementsByTagName('label')[0];
                if ($label) {
                    try {
                        $label->appendChild($node);
                    } catch (\Exception $e) {
                        $this->logger->warning($e->getMessage(), $this->getLoggingData());
                    }
                }
                $firstDiv = false;
            }
        }
        return $divs->getReturn();
    }

    public function isValid()
    {
        return $this->getDefaultValueId() !== 0;
    }

    public function getImages(): iterable
    {
        $currentValue = $this->getValue($this->getOption(), $this->selectedOptions);
        $this->setValueId((int) $currentValue->getId());
        $images = parent::getImages();
        return (array) $this->getSelectedImage($images);
    }

    protected function getSelectedImage(array $images)
    {
        return \reset($images);
    }

    /**
     * @return bool
     */
    public function isHiddenOption(): bool
    {
        return (bool) $this->getOption()->getData('hidden');
    }
}
