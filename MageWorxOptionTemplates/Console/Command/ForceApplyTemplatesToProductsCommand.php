<?php
/**
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\MageWorxOptionTemplates\Console\Command;

use Magento\Framework\App;
use BelVG\MageWorxOptionTemplates\Model\OptionSaver;
use Magento\Framework\App\ResourceConnection;
use MageWorx\OptionTemplates\Model\GroupFactory;
use MageWorx\OptionTemplates\Model\ResourceModel\Group\CollectionFactory
    as TemplateGroupCollectionFactory;
use Symfony\Component\Console\Command\Command;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ForceApplyTemplatesToProductsCommand extends Command
{
    protected $appState;
    protected $storeManager;
    protected $templateGroupCollectionFactory;
    protected $optionSaver;
    protected $_resource;
    protected $groupFactory;
    protected $collectionFactory;

    public function __construct(
        App\State $appState,
        TemplateGroupCollectionFactory $templateGroupCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        OptionSaver $optionSaver,
        ResourceConnection $resource,
        GroupFactory $groupFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
    )
    {
        $this->appState = $appState;
        $this->_resource = $resource;
        $this->optionSaver = $optionSaver;
        $this->storeManager = $storeManager;
        $this->groupFactory = $groupFactory;
        $this->collectionFactory = $collectionFactory;
        $this->templateGroupCollectionFactory = $templateGroupCollectionFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('belvg:mageworx:templates-force-apply')
            ->setDescription('Fix product options')
            ->addArgument('stores', InputArgument::OPTIONAL, 'Store IDs')
            ->addArgument('products', InputArgument::OPTIONAL, 'Product IDs, comma separated, - to skip')
            ->addArgument('templates', InputArgument::OPTIONAL, 'Template IDs, comma separated');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        return $this->appState->emulateAreaCode(
            App\Area::AREA_ADMINHTML,
            [$this, 'emulationCallback'], [$input, $io]);
    }

    public function emulationCallback(InputInterface $input, OutputInterface $output)
    {
        try {
            $storeIds = $this->getStores($input);
            $productIds = $this->getProducts($input);
            if (!$productIds || $productIds === '-') {
                $colection = $this->collectionFactory->create();
                $productsCleanup = $colection->getAllIds();

            } else {
                $productsCleanup = $productIds;
            }

            //additional logic for removing templates for products that shouldn't be there
            $this->removeRedundantTemplates($productsCleanup, $output);

            $templateIds = $this->getTemplates($input);
            if ($templateIds) {
                $templateGroupCollection = $this->getTemplateOptionCollection($templateIds);
            } else {
                $templateGroupCollection = $this->getTemplateOptionCollection();
            }

            foreach ($storeIds as $storeId) {
                $this->storeManager->setCurrentStore($storeId);
                foreach ($templateGroupCollection->getItems() as $templateGroup) {
                    if (!$productIds || $productIds === '-') {
                        $templatesProductIds = $templateGroup->getProducts();
                    } else {
                        $templatesGroupProductIds = $templateGroup->getProducts();
                        $templatesProductIds = array_intersect($templatesGroupProductIds, $productIds);
                        if (!$templatesProductIds) {
                            continue;
                        }
                    }
                    $templateGroup->setUpdProductIds($templatesProductIds);
                    $templateGroup->setAffectedProductIds($templatesProductIds);
                    $this->optionSaver->saveProductOptions(
                        $templateGroup,
                        $templateGroup->getOptionArray(),
                        OptionSaver::SAVE_MODE_UPDATE,
                        $storeId
                    );
                    $output->text(
                        sprintf(
                            "Store ID: %s Template ID: %d",
                            $storeId,
                            $templateGroup->getId()
                        )
                    );
                }
            }

            return Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->error($e->getMessage());
            return Cli::RETURN_FAILURE;
        }

    }

    protected function removeRedundantTemplates($productIds, OutputInterface $output)
    {
        if ($productIds) {
            $tableProductOption = $this->_resource->getTableName('catalog_product_option');
            $tableMageworxOptionTemplatesRelation = $this->_resource->getTableName('mageworx_optiontemplates_relation');
            $tableMageworxOptionTemplatesGroupOption = $this->_resource->getTableName('mageworx_optiontemplates_group_option');
            $connection = $this->_resource->getConnection();
            foreach ($productIds as $productId) {
                $redundantTemplates = $connection->select()
                    ->from($tableMageworxOptionTemplatesGroupOption)
                    ->join($tableMageworxOptionTemplatesRelation, "$tableMageworxOptionTemplatesGroupOption.group_id = $tableMageworxOptionTemplatesRelation.group_id")
                    ->where("$tableMageworxOptionTemplatesRelation.product_id=?", $productId)
                    ->reset('columns')
                    ->columns(["$tableMageworxOptionTemplatesGroupOption.mageworx_option_id"]);
                $sql = "DELETE  FROM $tableProductOption WHERE product_id=? AND mageworx_group_option_id NOT IN ($redundantTemplates)";
                $connection->query($sql, [$productId]);
                $output->text(
                    sprintf(
                        "Cleanup for product ID %s",
                        $productId
                    )
                );
            }
        }
    }

    protected function getStores(InputInterface $input)
    {
        $result = [];
        $storeIdsStr = $input->getArgument('stores');
        if ($storeIdsStr !== false) {
            $result = explode(',', $storeIdsStr);
        } else {
            $result[0] = '0';
            foreach ($this->storeManager->getStores() as $store) {
                $result[$store->getId()] = $store->getId();
            }
        }
        return $result;
    }

    protected function getProducts(InputInterface $input)
    {
        $productIdStr = $input->getArgument('products');
        if ($productIdStr === '-') {
            return $productIdStr;
        }
        return !empty($productIdStr)
            ? explode(',', $productIdStr)
            : [];
    }

    protected function getTemplates(InputInterface $input)
    {
        $templatesIdStr = $input->getArgument('templates');
        return !empty($templatesIdStr)
            ? explode(',', $templatesIdStr)
            : [];
    }

    protected function getTemplateOptionCollection($templateIds = null)
    {
        if ($templateIds) {
            return $this->templateGroupCollectionFactory->create()
                ->addFieldToFilter('group_id', ['in' => $templateIds]);
        } else {
            return $this->templateGroupCollectionFactory
                ->create();
        }
    }

}
