<?php

namespace BelVG\LayoutCustomizer\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConnectLayoutBySkuCommand extends Command
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $_state;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @var \BelVG\LayoutCustomizer\Api\Service\ConnectLayoutsBySkuInterface
     */
    private $_connectBySkuService;

    /**
     * ConnectLayoutBySkuCommand constructor.
     * @param \Magento\Framework\App\State $state
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \BelVG\LayoutCustomizer\Api\Service\ConnectLayoutsBySkuInterface $connectBySkuService
    ) {
        $this->_state = $state;
        $this->_storeManager = $storeManager;
        $this->_connectBySkuService = $connectBySkuService;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('belvg:layout:connect:by-sku')
            ->addArgument('sku', InputArgument::OPTIONAL, 'SKU (Magento product SKU)')
            ->setDescription('Sync products with layouts by SKU');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->_state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);

        $sku = $input->getArgument('sku');
        try {
            foreach ($this->_connectBySkuService->assign($sku) as $result) {
                $io->success('Updated: ' . print_r($result, true));
            }
            unset($result);
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }

        $io->success('Done');

        exit;
    }

}
