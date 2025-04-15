<?php

namespace BelVG\MageWorxOptionTemplates\Helper;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\State;
use MageWorx\OptionBase\Helper\Data as OriginalHelper;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class OptionBaseData extends OriginalHelper
{
    /**
     * @var State
     */
    protected $state;

    /**
     * @param ProductMetadataInterface $productMetadata
     * @param ObjectManagerInterface $objectManager
     * @param Context $context
     * @param ComponentRegistrarInterface $componentRegistrar
     * @param ReadFactory $readFactory
     * @param ManagerInterface $messageManager
     * @param ResponseInterface $response
     * @param JsonHelper $jsonHelper
     * @param ResourceConnection $resource
     * @param State $state
     * @param array $linkedAttributes
     * @param null $isDisabledConfigPath
     * @param null $isEnabledVisibilityPerCustomerGroup
     * @param null $isEnabledVisibilityPerStoreView
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        ObjectManagerInterface $objectManager,
        Context $context,
        ComponentRegistrarInterface $componentRegistrar,
        ReadFactory $readFactory,
        ManagerInterface $messageManager,
        ResponseInterface $response,
        JsonHelper $jsonHelper,
        ResourceConnection $resource,
        State $state,
        $linkedAttributes = [],
        $isDisabledConfigPath = null,
        $isEnabledVisibilityPerCustomerGroup = null,
        $isEnabledVisibilityPerStoreView = null
    )
    {
        $this->state = $state;
        parent::__construct(
            $productMetadata,
            $objectManager,
            $context,
            $componentRegistrar,
            $readFactory,
            $messageManager,
            $response,
            $jsonHelper,
            $resource,
            $linkedAttributes,
            $isDisabledConfigPath,
            $isEnabledVisibilityPerCustomerGroup,
            $isEnabledVisibilityPerStoreView
        );
    }

    /**
     * @param $conditions
     * @param $addWrap
     * @return array|mixed|string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function findMageWorxOptionTypeIdByConditions($conditions, $addWrap = true)
    {
        if (empty($conditions['option_id']) && !is_array($conditions['option_id'])) {
            return [];
        }
        if(count($conditions['option_id']) === 0){
            return [];
        }

        $whereCondition = "option_id IN (" . implode(',', $conditions['option_id']) . ")";

        if ($this->state->getAreaCode() == 'frontend' && !empty($this->mageworxOptionTypeIdCache[sha1($whereCondition)])) {
            return $this->mageworxOptionTypeIdCache[sha1($whereCondition)];
        }

        $connection = $this->resource->getConnection();
        $sql        = $connection->select()
            ->from($this->getOptionValueTableName($conditions['entity_type']))
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns('mageworx_option_type_id')
            ->distinct()
            ->where($whereCondition);

        $mageworxOptionTypeIds = $connection->fetchCol($sql);

        if ($addWrap) {
            $mageworxOptionTypeIds = array_map(
                function ($v) {
                    return "'" . $v . "'";
                },
                $mageworxOptionTypeIds
            );
        }

        $this->mageworxOptionTypeIdCache[sha1($whereCondition)] = $mageworxOptionTypeIds;

        return $mageworxOptionTypeIds;
    }

    /**
     * @param $conditions
     * @param $addWrap
     * @return array|mixed|string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function findOptionTypeIdByConditions($conditions, $addWrap = true)
    {
        if (empty($conditions['option_id']) && !is_array($conditions['option_id'])) {
            return [];
        }

        $whereCondition = "option_id IN (" . implode(',', $conditions['option_id']) . ")";

        if ($this->state->getAreaCode() == 'frontend' && !empty($this->optionTypeIdCache[sha1($whereCondition)])) {
            return $this->optionTypeIdCache[sha1($whereCondition)];
        }

        $connection = $this->resource->getConnection();
        $sql        = $connection->select()
            ->from($this->getOptionValueTableName($conditions['entity_type']))
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns('option_type_id')
            ->distinct()
            ->where($whereCondition);

        $ids = $connection->fetchCol($sql);

        if ($addWrap) {
            $ids = array_map(
                function ($v) {
                    return "'" . $v . "'";
                },
                $ids
            );
        }

        $this->optionTypeIdCache[sha1($whereCondition)] = $ids;

        return $ids;
    }

    /**
     * @param $conditions
     * @param $addWrap
     * @return array|mixed|string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function findMageWorxOptionIdByConditions($conditions, $addWrap = true)
    {
        if (empty($conditions['option_id']) && !is_array($conditions['option_id'])) {
            return [];
        }

        $whereCondition = "option_id IN (" . implode(',', $conditions['option_id']) . ")";

        if ($this->state->getAreaCode() == 'frontend' && !empty($this->mageworxOptionIdCache[sha1($whereCondition)])) {
            return $this->mageworxOptionIdCache[sha1($whereCondition)];
        }

        $connection = $this->resource->getConnection();
        $sql        = $connection->select()
            ->from($this->getOptionTableName($conditions['entity_type']))
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns('mageworx_option_id')
            ->distinct()
            ->where($whereCondition);

        $mageworxOptionIds = $connection->fetchCol($sql);

        if ($addWrap) {
            $mageworxOptionIds = array_map(
                function ($v) {
                    return "'" . $v . "'";
                },
                $mageworxOptionIds
            );
        }

        $this->mageworxOptionIdCache[sha1($whereCondition)] = $mageworxOptionIds;

        return $mageworxOptionIds;
    }

}
