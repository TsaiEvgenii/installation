<?php
namespace BelVG\LayoutCustomizer\Console\Command;

use BelVG\LayoutCustomizer\Helper\Data as DataHelper;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout as LayoutResource;
use Magento\Catalog\Model\ResourceModel\Product as ProductEntity;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Data;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProductsMissingLayoutCommand extends Command
{
    const OPTION_STORE = 'store';

    protected $collectionFactory;
    protected $productEntity;
    protected $layoutResource;
    protected $csvHelper;

    public function __construct(
        CollectionFactory $collectionFactory,
        ProductEntity $productEntity,
        LayoutResource $layoutResource,
        Helper\Csv $csvHelper)
    {
        $this->collectionFactory = $collectionFactory;
        $this->productEntity = $productEntity;
        $this->layoutResource = $layoutResource;
        $this->csvHelper = $csvHelper;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('belvg:layout:products-missing-layout')
            ->setDescription('Show list of products missing a layout')
            ->addOption(
                self::OPTION_STORE,
                null,
                InputOption::VALUE_OPTIONAL,
                'Store ID or code');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->getCollection($input) as $product) {
            $output->writeln(
                sprintf(
                    '%d,"%s","%s"',
                    $product->getId(),
                    $this->csvHelper->escape($product->getSku()),
                    $this->csvHelper->escape($product->getName())));
        }
    }

    protected function getCollection(InputInterface $input)
    {
        $collection = $this->collectionFactory
            ->create()
            ->addAttributeToSelect(['sku', 'name'])
            ->setOrder('entity_id', Data\Collection::SORT_ORDER_ASC);

        $store = $input->getOption(self::OPTION_STORE);
        if ($store) {
            $collection->setStore($store);
        }

        return $this->addMissingLayoutFilter($collection);
    }

    protected function addMissingLayoutFilter($collection)
    {
        // NOTE: attribute is global
        $attribute = $this->productEntity
            ->getAttribute(DataHelper::PRODUCT_LAYOUT_ATTR);
        $attrJoinCond = [
            'layout_attr.entity_id = e.entity_id',
            sprintf('attribute_id = %d', $attribute->getId()),
            'store_id = 0'
        ];

        $collection->getSelect()
            // Join layout_id attribute value
            ->joinLeft(
                ['layout_attr' => $attribute->getBackendTable()],
                implode(' AND ', $attrJoinCond),
                [])
            // Join layout
            ->joinLeft(
                ['layout' => $this->layoutResource->getMainTable()],
                'layout.layout_id = layout_attr.value',
                [])
            // Filter
            ->where('layout.layout_id IS NULL');
        // die((string)$collection->getSelect());
        return $collection;
    }
}
