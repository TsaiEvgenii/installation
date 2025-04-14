<?php
namespace BelVG\LayoutCustomizer\Controller\Adminhtml\Layout;

use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use BelVG\LayoutCustomizer\Api\LayoutRepositoryInterface;
use BelVG\LayoutCustomizer\Controller\Adminhtml\Layout as LayoutController;
use BelVG\LayoutCustomizer\Helper\Layout\Block as BlockHelper;

class CopyBlocks extends LayoutController
{
    protected $blockHelper;
    protected $layoutRepository;
    protected $searchCriteriaBuilderFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        BlockHelper $blockHelper,
        LayoutRepositoryInterface $layoutRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory)
    {
        $this->blockHelper = $blockHelper;
        $this->layoutRepository = $layoutRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        parent::__construct($context, $coreRegistry);
    }

    public function execute()
    {
        $layoutId = $this->getRequest()->getParam('layout_id');
        $targetLayoutIdsStr = $this->getRequest()->getParam('target_layout_ids');

        $targetLayoutIds = !empty($targetLayoutIdsStr)
            ? explode(',', $targetLayoutIdsStr)
            : [];
        unset($targetLayoutIdsStr);
        $targetLayoutIds = array_diff($targetLayoutIds, [$layoutId]); // remove dest. layout

        // Create redirect object
        $redirect = parent::createRedirect(
            '*/*/',
            ['store' => $this->getRequest()->getParam('store')]);

        // Check if target list is empty
        if (empty($targetLayoutIds)) {
            return $redirect;
        }

        try {
            // Get layout
            $origLayout = $this->layoutRepository->getById($layoutId, 0);

            // Get target list
            $searchCriteria = $this->searchCriteriaBuilderFactory
                ->create()
                ->addFilter('layout_id', $targetLayoutIds, 'in')
                ->create();
            $targetLayoutList = $this->layoutRepository->getList($searchCriteria);

            // Load block data
            $blockData = $this->blockHelper->stripIds(
                $this->blockHelper->load($origLayout->getLayoutId()));

            // Copy block data
            foreach ($targetLayoutList->getItems() as $targetLayout) {
                // Copy data
                $this->blockHelper->save($targetLayout->getLayoutId(), $blockData);
                // Add success message
                $message = __(
                    'Layout drawing copied from "%1" to "%2"',
                    $origLayout->getIdentifier(),
                    $targetLayout->getIdentifier());
                $this->messageManager->addSuccessMessage($message);
            }

        } catch (\Exception $de) {
            $this->messageManager->addErrorMessage($e->getMessage());
            throw $e; // TEST
        }

        return $redirect;
    }
}
