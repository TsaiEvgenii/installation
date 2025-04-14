<?php
namespace BelVG\LayoutCustomizer\Console\Command\Helper;

class Csv
{
    // NOTE: assuming string value, double quotes
    public function escape($string)
    {
        $string = str_replace('"', '""', $string);
        $string = str_replace('\\', '\\\\', $string);
        return $string;
    }
}
