<?php
namespace BelVG\LayoutCustomizer\Helper\Layout;

class Identifier
{
    const PART_SEPARATOR = '-';

    public function getPartSeparator()
    {
        return self::PART_SEPARATOR;
    }

    public function make($materialIdentifier, $family)
    {
        return !empty($materialIdentifier)
            ? $materialIdentifier . self::PART_SEPARATOR . $family
            : $family;
    }

    public function getMaterialIdentifier($identifier)
    {
        $pos = strpos($identifier, self::PART_SEPARATOR);
        return ($pos !== false)
            ? substr($identifier, 0, $pos)
            : null;
    }

    public function getFamily($identifier)
    {
        $pos = strpos($identifier, self::PART_SEPARATOR);
        return ($pos !== false)
            ? substr($identifier, $pos + 1)
            : $identifier;
    }
}
