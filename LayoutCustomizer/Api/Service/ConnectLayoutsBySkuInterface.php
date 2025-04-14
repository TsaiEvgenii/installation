<?php

namespace BelVG\LayoutCustomizer\Api\Service;

interface ConnectLayoutsBySkuInterface
{
    /**
     * @param string|null $sku
     * @return \Generator
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function assign(string $sku = null);

    /**
     * @param string|null $sku
     * @return \Generator
     */
    public function unassign(string $sku = null);

    /**
     * @param int $id
     * @return void
     */
    public function unassignById(int $id);
}
