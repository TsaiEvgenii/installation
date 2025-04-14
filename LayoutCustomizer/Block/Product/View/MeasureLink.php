<?php

namespace BelVG\LayoutCustomizer\Block\Product\View;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Variable\Model\VariableFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Model\Template\FilterProvider;
use BelVG\LayoutCustomizer\Model\Service\MeasureOldWindowContent;
use Psr\Log\LoggerInterface;
use Magento\Cms\Api\GetBlockByIdentifierInterface as BlockGetter;

class MeasureLink extends Template
{
    public const LOG_PREFIX = '[BelVG_LayoutCustomizer::MeasureLink] ';
    protected const POPUP_STATIC_BLOCK_ID = 'product-info-popup';

    /**
     * @var VariableFactory
     */
    protected VariableFactory $_varFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var BlockRepositoryInterface
     */
    protected BlockRepositoryInterface $_blockRepository;

    /**
     * @var ManagerInterface
     */
    protected ManagerInterface $messageManage;

    /**
     * @var FilterProvider
     */
    protected FilterProvider $_filterProvider;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var BlockGetter
     */
    protected $blockGetter;

    /**
     * @param Context $context
     * @param VariableFactory $varFactory
     * @param StoreManagerInterface $storeManager
     * @param BlockRepositoryInterface $blockRepository
     * @param FilterProvider $filterProvider
     * @param ManagerInterface $messageManage
     * @param LoggerInterface $logger
     * @param BlockGetter $blockGetter
     * @param array $data
     */
    public function __construct(
        Context $context,
        VariableFactory $varFactory,
        StoreManagerInterface $storeManager,
        BlockRepositoryInterface $blockRepository,
        FilterProvider $filterProvider,
        ManagerInterface $messageManage,
        LoggerInterface $logger,
        BlockGetter $blockGetter,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->_varFactory = $varFactory;
        $this->_storeManager = $storeManager;
        $this->_blockRepository = $blockRepository;
        $this->_filterProvider = $filterProvider;
        $this->messageManage = $messageManage;
        $this->logger = $logger;
        $this->blockGetter = $blockGetter;
    }

    /**
     * @param $staticBlockId
     * @return BlockInterface|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getStaticBlock($staticBlockId)
    {
        $storeId  = (int)$this->_storeManager->getStore()->getId();
        try{
            $block = $this->blockGetter->execute($staticBlockId, $storeId);
        } catch (LocalizedException $e) {
            $block = null;
        }
        return $block;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isPopupEnabled()
    {
        return !!$this->getStaticBlock(self::POPUP_STATIC_BLOCK_ID);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPopupContent()
    {
        $block = $this->getStaticBlock(self::POPUP_STATIC_BLOCK_ID);
        return $this->_filterProvider->getBlockFilter()->filter($block->getContent());
    }

    /**
     * @return string|null
     */
    public function getVariableValue(): ?string
    {
        $var = $this->_varFactory->create();
        $store = $this->_storeManager->getStore();
        $var->setStoreId($store->getId());
        $var->loadByCode(MeasureOldWindowContent::MEASURE_OLD_WINDOW);
        $content = $var->getValue(MeasureOldWindowContent::MEASURE_OLD_WINDOW);
        if (!$content) {
            $this->logger->alert(self::LOG_PREFIX . sprintf(
                    'You don\'t have %s content for %s store!',
                    MeasureOldWindowContent::MEASURE_OLD_WINDOW,
                    $store->getCode()
                ));
        }
        return $content;
    }
}
