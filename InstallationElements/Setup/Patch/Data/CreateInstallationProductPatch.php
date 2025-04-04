<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2024-2024.
 */
declare(strict_types=1);

namespace BelVG\InstallationElements\Setup\Patch\Data;

use BelVG\InstallationElements\Model\Config\InstallationProductConfig;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Area as AppArea;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchInterface;
use Psr\Log\LoggerInterface;

class CreateInstallationProductPatch implements DataPatchInterface
{
    private const LOG_PREFIX = '[BelVG_InstallationElements::CreateInstallationProductPatch]: ';

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly ProductInterfaceFactory $productFactory,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly StoreManagerInterface $storeManager,
        private readonly InstallationProductConfig $installationProductConfig,
        private readonly AppState $appState,
        private readonly LoggerInterface $logger
    ) {
    }

    public function apply() :PatchInterface
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        try{
            /** @var ProductInterface $product */
            $product = $this->productFactory->create();
            $product->setSku($this->installationProductConfig->getProductSku())
                ->setName($this->installationProductConfig->getProductName())
                ->setAttributeSetId($product->getDefaultAttributeSetId())
                ->setStatus(Status::STATUS_ENABLED)
                ->setPrice(0)
                ->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE)
                ->setWebsiteIds($this->getWebsitesIds())
                ->setSkipUrlRewrite(true) //see patch `ability-to-skip-url-rewrite-generation.patch`
                ->setCategoryIds([])
                ->setTypeId($this->installationProductConfig->getProductType())
                ->setStockData(array(
                        'use_config_manage_stock' => 0,
                        'manage_stock' => 0,
                        'is_in_stock' => 1,
                    )
                );

            $this->appState->emulateAreaCode(AppArea::AREA_FRONTEND, function () use ($product) {
                $this->productRepository->save($product);
            });

        } catch (\Throwable $t) {
            $this->logger->error(sprintf(
                self::LOG_PREFIX . ' %s, trace: %s',
                $t->getMessage(),
                chr(10) . $t->getTraceAsString()
            ));
        }
        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    private function getWebsitesIds(): array
    {
        $websiteIds = [];
        /** @var \Magento\Store\Api\Data\WebsiteInterface $website */
        foreach ($this->storeManager->getWebsites() as $website) {
            $websiteIds[] = $website->getId();
        }
        unset($website);

        return $websiteIds;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
