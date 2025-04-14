<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Block\Option\Type;

class Radio extends AbstractWrapperBlock
{
    public function process(string $result) :string
    {
        /**
         * @var $values \Generator
         */
        $values = $this->parser->get($result);
        foreach ($values as $value) {
            $node = $this->parser->createFragment();
            $valueId = $value->getAttribute('value');
            $this->setValueId((int)$valueId);
            $node->appendXML($this->toHtml());
            $label = $value->parentNode->getElementsByTagName('label')[0];
            if($label){
               try{
                   $label->appendChild($node);
               }catch (\Exception $e){
                   if($this->getOption() && $this->getOption()->getIsRequire()){
                       $this->logger->warning($e->getMessage(), $this->getLoggingData());
                   }
               }
            }
        }
        return $values->getReturn();
    }
}
