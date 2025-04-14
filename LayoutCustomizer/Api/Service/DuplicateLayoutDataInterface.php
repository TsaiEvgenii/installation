<?php
namespace BelVG\LayoutCustomizer\Api\Service;

use BelVG\LayoutCustomizer\Api\Data\LayoutInterface;

interface DuplicateLayoutDataInterface
{
    public function copyAndSave(LayoutInterface $origObject);
}
