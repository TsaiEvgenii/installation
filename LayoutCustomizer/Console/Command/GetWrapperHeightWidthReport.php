<?php
/*
 * @package Vinduesgrossisten.
 * @author Simonchik <alexandr.simonchik@gmail.com>
 * @Copyright
 */

namespace BelVG\LayoutCustomizer\Console\Command;

use Magento\Framework\App\ResourceConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetWrapperHeightWidthReport extends Command
{
    private $io;

    private $resource;

    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;

        parent::__construct();
    }

    /**
     *
     */
    protected function configure()
    {
        $options = [];

        $this
            ->setName('belvg:layout:get_wrapper_height_width')
            ->setDescription('Get report with `Layout identifier`, `Height`, `Width`')
            ->setDefinition($options);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->io = new SymfonyStyle($input, $output);

            $output->writeln($this->getRow(['Layout ID', 'Layout Identifier (SKU)', 'Height', 'Width']));
            foreach ($this->getCollection() as $layout) {
                $output->writeln($this->getRow([
                    $layout['layout_id'],
                    $layout['identifier'],
                    $layout['height'],
                    $layout['width']
                ]));
            }
            unset($layout);
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $this->io->error($e->getMessage());

            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }

    /**
     * Prepare CSV row
     *
     * @param $data
     * @return string
     */
    protected function getRow($data)
    {
        return implode(',', $data);
    }

    /**
     * Get collection for report
     *
     * @return array
     */
    protected function getCollection()
    {
        $connection = $this->resource->getConnection();

        return $connection->fetchAll('
            SELECT layout.layout_id, layout.identifier,
                   layout_block.height, layout_block.width
            FROM ' . $connection->getTableName('belvg_layoutcustomizer_layout_block') . ' layout_block
            JOIN ' . $connection->getTableName('belvg_layoutcustomizer_layout') . ' layout ON (layout.layout_id = layout_block.layout_id)
            WHERE `parent_id` IS NULL
        ');
    }
}
