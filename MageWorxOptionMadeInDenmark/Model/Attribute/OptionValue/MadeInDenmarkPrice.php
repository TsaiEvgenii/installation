<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023.
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionMadeInDenmark\Model\Attribute\OptionValue;


use MageWorx\OptionBase\Model\AttributeInterface;
use MageWorx\OptionBase\Model\Product\Option\AbstractAttribute;

class MadeInDenmarkPrice
    extends AbstractAttribute
    implements AttributeInterface
{
    public function getName(): string
    {
        return 'made_in_denmark_price';
    }

    public function hasOwnTable(): bool
    {
        return true;
    }

    public function prepareDataForFrontend($data): array
    {
        return [];
    }
}