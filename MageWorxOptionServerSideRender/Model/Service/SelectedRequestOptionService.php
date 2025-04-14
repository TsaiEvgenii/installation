<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2021
 */
declare(strict_types=1);

namespace BelVG\MageWorxOptionServerSideRender\Model\Service;

use BelVG\MageWorxOptionServerSideRender\Api\Data\SelectedOptionInterface;
use BelVG\MageWorxOptionServerSideRender\Model\Dto\SelectedOption;
use BelVG\MageWorxOptionServerSideRender\Model\SelectedOptionValue;
use BelVG\MageWorxOptionServerSideRender\Model\Spi\SelectedRequestOptionInterface;

class SelectedRequestOptionService implements SelectedRequestOptionInterface
{
    const PATTERN = '$^(|options(_|-))>?(?<option_id>\d+)$';

    const MATERIAL_PATTERN = '$^(key_)(?<option_key>.*)$';


    /**
     * @param array $requestData
     * @return SelectedOptionInterface[]
     */
    public function get(array $requestData) :iterable
    {
        $selectedOptions = [];

        //options from another material
        foreach ($requestData as $optionIdData => $optionValue) {
            $optionValueObject = new SelectedOptionValue(['value'=>$optionValue]);
            if (preg_match(self::MATERIAL_PATTERN, (string)$optionIdData, $matches)) {
                $selectedOptions[] = new SelectedOption(
                    [SelectedOptionInterface::OPTION_KEY=>$matches['option_key'],
                        SelectedOptionInterface::VALUE=>$optionValueObject]
                );
            }
        }

        foreach ($requestData as $optionIdData => $optionValue) {
            $optionValueObject = new SelectedOptionValue(['value'=>$optionValue]);
            if (preg_match(self::PATTERN, (string)$optionIdData, $matches)) {
                $selectedOptions[] = new SelectedOption(
                    [SelectedOptionInterface::OPTION_ID=>(int)$matches['option_id'],
                            SelectedOptionInterface::VALUE=>$optionValueObject]
                );
            }
        }
        return $selectedOptions;
    }
}
