<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024.
 */

namespace BelVG\MeasurementTool\Ui\Component\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * DataProvider component.
 */
class MeasurementTool  extends AbstractDataProvider
{
    protected PoolInterface $pool;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        protected \Magento\Framework\Data\Form\FormKey $formKey,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->pool = $pool;
    }

    public function getMeta(): array
    {
        $meta = parent::getMeta();
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }
        return $meta;
    }

    public function getData(): array
    {
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }
        return $this->data;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        // ignore
    }
}
