<?php
/**
 * @package Vinduesgrossisten.
 * @author Ivanenko <a.ivanenko@belvg.com>
 * @Copyright
 */

declare(strict_types=1);

namespace BelVG\LayoutCustomizer\Setup\Patch\Data;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use BelVG\LayoutCustomizer\Model\Service\MeasureOldWindowContent;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;

/**
 * Class AddMeasureOldWindowContentToVariableTable
 *
 * @package BelVG\LayoutCustomizer\Setup\Patch\Data
 */
class AddMeasureOldWindowContentToVariableTable implements DataPatchInterface
{
    const SKANVA_NO = 'skanvano';

    /**
     * @var MeasureOldWindowContent
     */
    protected MeasureOldWindowContent $measureOldWindowContent;
    private StoreManager $storeManager;

    /**
     * @param MeasureOldWindowContent $measureOldWindowContent
     * @param StoreManager $storeManager
     */
    public function __construct(MeasureOldWindowContent $measureOldWindowContent, StoreManager $storeManager )
    {
        $this->measureOldWindowContent = $measureOldWindowContent;
        $this->storeManager = $storeManager;
    }

    public static function getDependencies(): array
    {
        /**
         * To define a dependency in a patch, add the method public static function getDependencies()
         * to the patch class and return the class names of the patches this patch depends on.
         * The dependency can be in any module.
         */
        return [];
    }

    public function getAliases(): array
    {
        /**
         * This internal Magento method, that means that some patches with time can change their names,
         * but changing name should not affect installation process, that's why if we will change name of the patch
         * we will add alias here
         */
        return [];
    }

    public function apply()
    {
        $this->changeMeasureOldWindowContentToVariableTableNOStore();
    }

    private function changeMeasureOldWindowContentToVariableTableNOStore(): void
    {
        $data = [
            'Inntast dine karmmål',
            'https://skanva.no/kundesenter/oppmaaling-vinduer-doerer',
            'Slik måler du opp',
            'Denne tegning er veiledende og sett utenfra. Alle mål er karmmål.',
        ];
        try{
            $store = $this->storeManager->getStore(self::SKANVA_NO);
            $this->measureOldWindowContent->saveMeasureOldWindowContent($data, (int)$store->getId());
        }catch (NoSuchEntityException $exception){

        }
    }
}
