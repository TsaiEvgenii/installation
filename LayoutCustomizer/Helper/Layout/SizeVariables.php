<?php
/**
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\LayoutCustomizer\Helper\Layout;


class SizeVariables
{
    const LAYOUT_HEIGHT = 'layout_height';
    const LAYOUT_WIDTH = 'layout_width';
    const LAYOUT_SECTION_SIZES = 'layout_section_sizes';

    public function camelize($input, $separator = '_')
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }

    public function getLayoutVariablesPool() {
        return [
            self::LAYOUT_HEIGHT,
            self::LAYOUT_WIDTH,
            self::LAYOUT_SECTION_SIZES,
        ];
    }
}
