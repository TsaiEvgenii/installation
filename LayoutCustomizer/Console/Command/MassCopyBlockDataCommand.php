<?php
namespace BelVG\LayoutCustomizer\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SortOrderBuilder;
use BelVG\LayoutCustomizer\Helper\Layout\Block as BlockHelper;
use BelVG\LayoutCustomizer\Helper\Layout\Identifier as IdentifierHelper;
use BelVG\LayoutCustomizer\Model\LayoutRepository;
use BelVG\LayoutMaterial\Model\LayoutMaterialRepository;

class MassCopyBlockDataCommand extends Command
{
    protected $identifierHelper;
    protected $blockHelper;
    protected $layoutRepository;
    protected $materialRepository;
    protected $searchCriteriaBuilderFactory;
    protected $sortOrderBuilder;

    protected $materialIdentifiers;

    public function __construct(
        IdentifierHelper $identifierHelper,
        BlockHelper $blockHelper,
        LayoutRepository $layoutRepository,
        LayoutMaterialRepository $materialRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        SortOrderBuilder $sortOrderBuilder
)
    {
        $this->identifierHelper = $identifierHelper;
        $this->blockHelper = $blockHelper;
        $this->layoutRepository = $layoutRepository;
        $this->materialRepository = $materialRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->sortOrderBuilder = $sortOrderBuilder;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('belvg:layout:mass-copy-drawings')
            ->setDescription('Copy layout drawings')
            ->addOption(
                'src-prefix',
                null,
                InputOption::VALUE_REQUIRED,
                'Source layout identifier prefix',
                'TR-')
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Do not actually copy data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get options
        $srcPrefix = $input->getOption('src-prefix');
        $dryRun    = $input->getOption('dry-run');

        // Get src layouts
        $srcLayoutList = $this->getSourceList($srcPrefix);
        // Output
        $output->writeln(
            sprintf('Source layout identifier prefix: "%s"', $srcPrefix));
        $output->writeln(
            sprintf('%d source layouts found', $srcLayoutList->getTotalCount()));
        $output->writeln(
            sprintf('Materials: %s', implode(', ', $this->getMaterialIdentifiers())));
        $output->writeln('');
        // Process src layouts
        $copyCount = 0;
        foreach ($srcLayoutList->getItems() as $srcLayout) {
            $identifier = $srcLayout->getIdentifier();

            // Output
            $output->writeln($identifier);

            // Get dst layouts
            $family = $this->identifierHelper->getFamily($identifier);
            $dstIdentifiers = array_diff(
                $this->generateDestIdentifiers($family),
                [$identifier]);
            $dstLayoutList = $this->getListByIdentifiers($dstIdentifiers);

            // Get block data
            $blockData = $this->blockHelper->stripIds(
                $this->blockHelper->load($srcLayout->getLayoutId()));

            // Copy to dst layouts
            foreach ($dstLayoutList->getItems() as $dstLayout) {
                // Output
                $output->writeln(' - ' . $dstLayout->getIdentifier());

                // Copy
                if (!$dryRun) {
                    $this->blockHelper->save($dstLayout->getLayoutId(), $blockData);
                }
                ++$copyCount;
            }
        }
        // Output
        $output->writeln('');
        $output->writeln(
            sprintf(
                '%d layouts %s',
                $copyCount,
                ($dryRun ? 'to update' : 'updated')));
    }

    protected function getSourceList($srcPrefix)
    {
        $sortOrder = $this->sortOrderBuilder
            ->setField('identifier')
            ->setAscendingDirection()
            ->create();
        $searchCriteria = $this->searchCriteriaBuilderFactory
            ->create()
            ->addFilter('identifier', $srcPrefix . '%', 'like')
            ->addSortOrder($sortOrder)
            ->create();
        return $this->layoutRepository->getList($searchCriteria);
    }

    protected function getListByIdentifiers(array $identifiers)
    {
        $sortOrder = $this->sortOrderBuilder
            ->setField('identifier')
            ->setAscendingDirection()
            ->create();
        $searchCriteria = $this->searchCriteriaBuilderFactory
            ->create()
            ->addFilter('identifier', $identifiers, 'in')
            ->addSortOrder($sortOrder)
            ->create();
        return $this->layoutRepository->getList($searchCriteria);
    }

    protected function generateDestIdentifiers($family)
    {
        return array_map(function($materialIdentifier) use ($family) {
            return $this->identifierHelper->make($materialIdentifier, $family);
        }, $this->getMaterialIdentifiers());
    }

    protected function getMaterialIdentifiers()
    {
        if (is_null($this->materialIdentifiers)) {
            $sortOrder = $this->sortOrderBuilder
                ->setField('identifier')
                ->setAscendingDirection()
                ->create();
            $searchCriteria = $this->searchCriteriaBuilderFactory
                ->create()
                ->addSortOrder($sortOrder)
                ->create();
            $materialList = $this->materialRepository->getList($searchCriteria);
            $this->materialIdentifiers = array_map(function($material) {
                return $material->getIdentifier();
            }, $materialList->getItems());
        }
        return $this->materialIdentifiers;
    }
}
