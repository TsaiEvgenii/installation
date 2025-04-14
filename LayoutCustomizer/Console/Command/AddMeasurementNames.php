<?php
namespace BelVG\LayoutCustomizer\Console\Command;

use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Block as BlockResource;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Measurement as MeasurementResource;
use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Measurement\CollectionFactory
    as MeasurementCollectionFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AddMeasurementNames extends command
{
    protected $blockResource;
    protected $measurementResource;
    protected $measurementCollectionFactory;

    // [layout_id => [name => true]]
    protected $names = [];

    public function __construct(
        BlockResource $blockResource,
        MeasurementResource $measurementResource,
        MeasurementCollectionFactory $measurementCollectionFactory)
    {
        $this->blockResource = $blockResource;
        $this->measurementResource = $measurementResource;
        $this->measurementCollectionFactory = $measurementCollectionFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('belvg:layout:add-measurement-names')
            ->setDescription('Generate names for unnamed configurable measurements')
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Do not actually update');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dryRun = $input->getOption('dry-run');

        // Root block measurements
        foreach ($this->getRootBlockMeasurementCollection() as $measurement) {
            if (!$measurement->getName()) {
                $measurement->setName($measurement->getType()); // width/height
                if (!$dryRun)
                    $this->measurementResource->save($measurement);
                $output->writeln($measurement->getName());
            }
            $this->addName($measurement->getLayoutId(), $measurement->getName());
        }

        // Non-root block measurements
        foreach ($this->getMeasurementCollection() as $measurement) {
            if (!$measurement->getName()
                || $this->nameExists($measurement->getLayoutId(), $measurement->getName()))
            {
                $name = $this->newName($measurement->getLayoutId(), $measurement->getType());
                $measurement->setName($name);
                if (!$dryRun)
                    $this->measurementResource->save($measurement);
                $output->writeln($measurement->getName());
            }
            $this->addName($measurement->getLayoutId(), $measurement->getName());
        }
    }

    protected function addName($layoutId, $name)
    {
        $this->names[$layoutId][$name] = true;
    }

    protected function nameExists($layoutId, $name)
    {
        return isset($this->names[$layoutId][$name]);
    }

    protected function newName($layoutId, $type)
    {
        $idx = 1;
        do {
            $name = sprintf('%s%d', $type, $idx++);
        } while ($this->nameExists($layoutId, $name));
        return $name;
    }

    protected function getRootBlockMeasurementCollection()
    {
        $blockTable = $this->blockResource->getMainTable();
        return $this->makeMeasurementCollection()
            ->join(
                ['block' => $blockTable],
                'block.block_id = main_table.block_id AND block.parent_id IS NULL',
                ['layout_id' => 'block.layout_id']);
    }

    protected function getMeasurementCollection()
    {
        $blockTable = $this->blockResource->getMainTable();
        return  $this->makeMeasurementCollection()
            ->join(
                ['block' => $blockTable],
                'block.block_id = main_table.block_id AND block.parent_id IS NOT NULL',
                ['layout_id' => 'block.layout_id']);
    }
    protected function makeMeasurementCollection()
    {
        return $this->measurementCollectionFactory
            ->create()
            ->addFieldToFilter('is_customizable', true);
            // ->addFieldToFilter('name', ['isnull' => true]);
    }
}
