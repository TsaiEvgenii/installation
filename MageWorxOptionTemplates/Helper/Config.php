<?php
/**
 * @package Vinduesgrossisten
 *  * @author  Tsai<tsai.evgenii@belvg.com>
 *  * @Copyright
 */

namespace BelVG\MageWorxOptionTemplates\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Store\Model\ScopeInterface as StoreScopeModel;
use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Config
 *
 * @package BelVG\MageWorxOptionTemplates\Helper
 */
class Config extends AbstractHelper
{
    /** @var string */
    const MATERIAL_PROP_UPDATE_PREFIX = 'belvg_mageworx_option_templates_update';

    /** @var string  */
    const CSV_FIELD_NAME = 'csv_file';

    /** @var string  */
    const UPDATE_OPTIONS = 'product_edit_options';

    /** @var string  */
    const CSV_FILE_UPLOAD_DIR = 'MageWorxOptionTemplatesUpdateFile';

    /** @var DirectoryList  */
    protected $directoryList;

    /**
     * Config constructor.
     *
     * @param DirectoryList $directoryList
     * @param Context       $context
     */
    public function __construct(
        DirectoryList $directoryList,
        Context $context)
    {
        parent::__construct($context);
        $this->directoryList = $directoryList;
    }

    public function getConfig($field, $store = null, $scope = 'general')
    {
        return $this->scopeConfig->getValue(
            self::MATERIAL_PROP_UPDATE_PREFIX . '/' . $scope . '/' . $field,
            StoreScopeModel::SCOPE_STORE,
            $store
        );
    }

    public function getCsvFilePath($storeId = null)
    {
        try {
            return $this->directoryList->getPath('media')
                . '/'
                . self::CSV_FILE_UPLOAD_DIR
                . '/'
                . $this->getConfig(self::CSV_FIELD_NAME, $storeId);
        } catch (FileSystemException $e) {
            $this->_logger->error($e->getMessage());
            return null;
        }
    }
}
