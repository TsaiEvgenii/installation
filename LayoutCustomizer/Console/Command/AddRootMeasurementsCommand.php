<?php
namespace BelVG\LayoutCustomizer\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BelVG\LayoutCustomizer\Helper\Data as DataHelper;
use BelVG\LayoutCustomizer\Model\Layout\Block;
use BelVG\LayoutCustomizer\Model\Layout\MeasurementFactory;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block\CollectionFactory
    as BlockCollectionFactory;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Measurement\CollectionFactory
    as MeasurementCollectionFactory;

class AddRootMeasurementsCommand extends Command
{
    protected $dataHelper;
    protected $measurementFactory;
    protected $blockCollectionFactory;
    protected $measurementCollectionFactory;

    public function __construct(
        DataHelper $dataHelper,
        MeasurementFactory $measurementFactory,
        BlockCollectionFactory $blockCollectionFactory,
        MeasurementCollectionFactory $measurementCollectionFactory
    )
    {
        $this->dataHelper = $dataHelper;
        $this->measurementFactory = $measurementFactory;
        $this->blockCollectionFactory = $blockCollectionFactory;
        $this->measurementCollectionFactory = $measurementCollectionFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('belvg:layout:add-root-measurements')
            ->setDescription('Add root block measurements');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        // Get width parameter
        $widthParamId = $this->dataHelper->getOverallWidthParamId();
        if (!$widthParamId) {
            $io->error('Width option is not set');
            return;
        }

        // Get height parameter
        $heightParamId = $this->dataHelper->getOverallHeightParamId();
        if (!$heightParamId) {
            $io->error('Height option is not set');
            return;
        }

        // Process root blocks
        $blocks = $this->loadBlockCollection();
        foreach ($blocks as $block) {
            $this->processBlock($io, $block, $widthParamId, $heightParamId);
        }
    }

    protected function processBlock(SymfonyStyle $io, Block $block, $widthParamId, $heightParamId)
    {
        $io->text($block->getLayoutIdentifier());
        $this->updateMeasurement($io, $block, 'width', $widthParamId);
        $this->updateMeasurement($io, $block, 'height', $heightParamId);
    }

    protected function updateMeasurement(SymfonyStyle $io, Block $block, $type, $paramId)
    {
        $measurement = $this->getMeasurementByType($block, $type);
        $isNew = !$measurement->getId();
        if (!$measurement->getId()
            || $measurement->getParamId() != $paramId
            || !$measurement->getIsCustomizable())
        {
            $measurement
                ->setParamId($paramId)
                ->setIsCustomizable(true);
            $measurement->save();
            $io->success(sprintf('%s is %s', $type, ($isNew ? 'added' : 'updated')));
        }
    }

    protected function getMeasurementByType(Block $block, $type)
    {
        $measurements = $block->getMeasurements();

        // Find in block measurements
        $result = null;
        foreach ($measurements as $measurement) {
            if ($measurement->getType() == $type) {
                $result = $measurement;
                break;
            }
        }

        // Create if not found
        if (!$result) {
            $result = $this->measurementFactory
                ->create()
                ->setBlockId($block->getId())
                ->setType($type)
                ->setIsCustomizable(true)
                ->setSortOrder(count($measurements))
                ->setParams([
                    'adjustment1' => 0,
                    'adjustment2' => 0,
                    'offset'      => 0,
                    'placement'   => ($type == 'width') ? 'top' : 'right'
                ]);
        }

        return $result;
    }

    protected function loadBlockCollection()
    {
        // Create root block collection
        $blockCollection = $this->blockCollectionFactory
            ->create()
            ->joinLayoutIdentifiers()
            ->addIsRootFilter(true);

        // Create measurement collection
        $measurementCollection = $this->measurementCollectionFactory
            ->create()
            ->addBlockFilter($blockCollection->getAllIds());

        // Add measurements to blocks
        foreach ($measurementCollection as $measurement) {
            $block = $blockCollection->getItemById($measurement->getBlockId());
            $block->addMeasurement($measurement);
        }

        return $blockCollection;
    }
}
