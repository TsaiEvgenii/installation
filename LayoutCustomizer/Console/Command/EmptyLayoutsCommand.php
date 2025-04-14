<?php
namespace BelVG\LayoutCustomizer\Console\Command;

use BelVG\LayoutCustomizer\Model\ResourceModel\Layout\CollectionFactory;
use Magento\Framework\Data;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EmptyLayoutsCommand extends Command
{
    const OPTION_ONLY_IDS = 'print-ids';
    const OPTION_ONLY_IDENTIFIERS = 'print-identifiers';

    protected $collectionFactory;
    protected $csvHelper;

    public function __construct(
        CollectionFactory $collectionFactory,
        Helper\Csv $csvHelper)
    {
        $this->collectionFactory = $collectionFactory;
        $this->csvHelper = $csvHelper;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('belvg:layout:empty')
            ->setDescription('Show empty layouts')
            ->addOption(
                self::OPTION_ONLY_IDS,
                null,
                InputOption::VALUE_NONE,
                'Print only IDs')
            ->addOption(
                self::OPTION_ONLY_IDENTIFIERS,
                null,
                InputOption::VALUE_NONE,
                'Print only identifiers');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->getEmptyLayoutCollection($input) as $layout) {
            if ($input->getOption(self::OPTION_ONLY_IDS)) {
                $output->writeln($layout->getId());

            } elseif ($input->getOption(self::OPTION_ONLY_IDENTIFIERS)) {
                $output->writeln($layout->getIdentifier());

            } else {
                $output->writeln(
                    sprintf(
                        '%d,"%s"',
                        $layout->getId(),
                        $this->csvHelper->escape($layout->getIdentifier())));
            }
        }
    }

    protected function getEmptyLayoutCollection(InputInterface $input)
    {
        /** @var \BelVG\LayoutCustomizer\Model\ResourceModel\Layout\Collection $collection */
        $collection = $this->collectionFactory
            ->create()
            ->addIsEmptyFilter();
        if ($input->getOption(self::OPTION_ONLY_IDS)) {
            $collection->setOrder('layout_id', Data\Collection::SORT_ORDER_ASC);
        } else {
            $collection->setOrder('family_id', Data\Collection::SORT_ORDER_ASC);
        }
        return $collection;
    }
}
