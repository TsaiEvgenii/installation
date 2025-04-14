<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionServerSideRender\Model\Service;

use \BelVG\Stdlib\src\StringWrapper\MbString;

class Parser implements ParserInterface
{
    private MbString $mbString;

    private \DOMDocument $dom;
    private string $path;

    /**
     * HtmlParser constructor.
     * @param MbString $mbString
     * @param string $path
     */
    public function __construct(MbString $mbString, string $path = '//input')
    {
        $this->mbString = $mbString;
        $this->path = $path;
    }

    public function get(string $result):\Generator
    {
        if ($result == '') {
            return $result;
        }
        $this->dom = new \DOMDocument();
        $this->dom->preserveWhiteSpace = false;
        libxml_use_internal_errors(true);
//        $this->mbString->setEncoding('UTF-8', 'html-entities');
        $result = $this->mbString->convert($result);
        $this->dom->loadHTML($result);
        libxml_clear_errors();
        $xpath = new \DOMXPath($this->dom);
        $values = $xpath->query($this->path);
        /**
         * @var \DOMElement $value
         */
        foreach ($values as $value) {
            yield $value;
        }
        if ($values->count()) {
            $resultBody = $this->dom->getElementsByTagName('body')->item(0);//$dom->saveHTML();
            $result = $this->getInnerHtml($resultBody);
        }
        return $result;
    }


    public function createFragment()
    {
        return $this->dom->createDocumentFragment();
    }

    private function getInnerHtml($node)
    {
        $innerHTML= '';
        $children = $node->childNodes;
        foreach ($children as $child) {
            $innerHTML .= $child->ownerDocument->saveXML($child);
        }
        return $innerHTML;
    }
}
