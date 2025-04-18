<?php
declare(strict_types=1);

namespace BelVG\OrderUpgrader\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Option types dropdown for option values configuration
 */
class OptionTypeRenderer extends Select
{
    /**
     * Config path for option types
     */
    private const XML_PATH_OPTION_TYPES = 'belvg_order_upgrader/options_config/types';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getOptionTypes());
        }
        return parent::_toHtml();
    }

    /**
     * Get option types from config
     *
     * @return array
     */
    private function getOptionTypes(): array
    {
        $options = [];

        $options[] = ['label' => __('-- Please Select --'), 'value' => ''];

        $types = $this->scopeConfig->getValue(self::XML_PATH_OPTION_TYPES);
        if (!$types) {
            return $options;
        }

        try {
            if (!is_array($types)) {
                $types = $this->serializer->unserialize($types);
            }
            if (is_array($types)) {
                foreach ($types as $type) {
                    if (isset($type['code']) && isset($type['label'])) {
                        $options[] = ['label' => $type['label'], 'value' => $type['code']];
                    }
                }
            }
        } catch (\Exception $e) {
        }

        return $options;
    }
}