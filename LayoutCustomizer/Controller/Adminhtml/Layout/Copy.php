<?php
namespace BelVG\LayoutCustomizer\Controller\Adminhtml\Layout;

use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use BelVG\LayoutCustomizer\Controller\Adminhtml\Layout as LayoutController;
use BelVG\LayoutCustomizer\Helper\Layout\Identifier as IdentifierHelper;
use BelVG\LayoutCustomizer\Api\LayoutRepositoryInterface;
use BelVG\LayoutCustomizer\Api\Service\DuplicateLayoutDataInterface;
use BelVG\LayoutMaterial\Model\LayoutMaterialRepository;

class Copy extends LayoutController
{
    protected $identifierHelper;
    protected $layoutRepository;
    protected $duplicateLayoutService;
    protected $materialRepository;
    protected $searchCriteriaBuilderFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        IdentifierHelper $identifierHelper,
        LayoutRepositoryInterface $layoutRepository,
        DuplicateLayoutDataInterface $duplicateLayoutService,
        LayoutMaterialRepository $materialRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->identifierHelper = $identifierHelper;
        $this->layoutRepository = $layoutRepository;
        $this->duplicateLayoutService = $duplicateLayoutService;
        $this->materialRepository = $materialRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        parent::__construct($context, $coreRegistry);
    }

    public function execute()
    {
        $layoutId = $this->getRequest()->getParam('layout_id');
        $materialIdsStr = $this->getRequest()->getParam('material_ids');

        $materialIds = !empty($materialIdsStr)
            ? explode(',', $materialIdsStr)
            : [];
        unset($materialIdsStr);

        // Create redirect object
        $redirect = parent::createRedirect(
            '*/*/',
            ['store' => $this->getRequest()->getParam('store')]);
        if (empty($materialIds)) {
            return $redirect;
        }

        try {
            // Get layout
            $origLayout = $this->layoutRepository->getById($layoutId, 0);

            // Get family layouts
            $familyMaterialIds = [];
            $familyIdentifiersByMaterialId = [];
            if ($origLayout->getFamilyId()) {
                $searchCriteria = $this->searchCriteriaBuilderFactory
                    ->create()
                    ->addFilter('family_id', $origLayout->getFamilyId())
                    ->create();
                $layoutList = $this->layoutRepository->getList($searchCriteria);
                // Family material IDs
                $familyMaterialIds = array_map(
                    function($layout) { return $layout->getLayoutmaterialId(); },
                    $layoutList->getItems());
                // Family identifiers
                $familyIdentifiers = array_map(
                    function($layout) { return $layout->getIdentifier(); },
                    $layoutList->getItems());
                // Family identifiers by material ID (selected only)
                $familyIdentifiersByMaterialId = [];
                foreach ($layoutList->getItems() as $layout) {
                    $materialId = $layout->getLayoutmaterialId();
                    if (in_array($materialId, $materialIds)) {
                        isset($familyIdentifiersByMaterialId[$materialId])
                            or $familyIdentifiersByMaterialId[$materialId] = [];
                        $familyIdentifiersByMaterialId[$materialId][] = $layout->getIdentifier();
                    }
                }
            }

            // Get selected materials
            $searchCriteria = $this->searchCriteriaBuilderFactory
                ->create()
                ->addFilter('layoutmaterial_id', $materialIds, 'in')
                ->create();
            $materialList = $this->materialRepository->getList($searchCriteria);

            // Generate new layout identifiers
            $newIdentifiersByMaterialId = [];
            $family = $this->identifierHelper->getFamily($origLayout->getIdentifier());
            foreach ($materialList->getItems() as $material) {
                $newIdentifiersByMaterialId[$material->getId()] = $this->identifierHelper
                    ->make($material->getIdentifier(), $family);
            }

            $existingIdentifiersByMaterialId = [];
            $existingIdentifiers = [];
            if (!empty($newIdentifiersByMaterialId)) {
                // Get existing layouts with new identifiers
                $searchCriteria = $this->searchCriteriaBuilderFactory
                    ->create()
                    ->addFilter('identifier', array_values($newIdentifiersByMaterialId), 'in')
                    ->create();
                $existingLayoutList = $this->layoutRepository->getList($searchCriteria);
                // List of existing identifiers
                $existingIdentifiers = array_map(
                    function($layout) {
                        return $layout->getIdentifier();
                    },
                    $existingLayoutList->getItems());
                // Existing identifiers by material ID
                $existingIdentifiersByMaterialId = [];
                foreach ($existingLayoutList->getItems() as $layout) {
                    $materialId = $layout->getLayoutmaterialId();
                    isset($existingIdentifiersByMaterialId[$materialId])
                        or $existingIdentifiersByMaterialId[$materialId] = [];
                    $existingIdentifiersByMaterialId[$materialId][] = $layout->getIdentifier();
                }

                // Remove family materials from list
                $newIdentifiersByMaterialId = array_diff_key(
                    $newIdentifiersByMaterialId,
                    array_fill_keys($familyMaterialIds, true));

                // Remove existing identifiers from list
                $newIdentifiersByMaterialId = array_diff(
                    $newIdentifiersByMaterialId,
                    $existingIdentifiers);
            }

            // Messages
            $successMessages = [];
            $warningMessages = [];

            // Create layout copies
            foreach ($newIdentifiersByMaterialId as $materialId => $identifier) {
                // Create
                $this->duplicateLayoutService->copyAndSave(
                    $origLayout,
                    function($layoutCopy) use ($materialId, $identifier) {
                        $layoutCopy->setLayoutmaterialId($materialId);
                        $layoutCopy->setIdentifier($identifier);
                    });
                // Add success message
                $successMessages[] = (string) __(
                    'Layout copy "%1" created for material "%2"',
                    $identifier,
                    $this->getMaterialIdentifier($materialId));
            }

            // Collect warning messages for family materials
            $existingFamilyIdentifiersByMaterialId = array_diff_key(
                $familyIdentifiersByMaterialId,
                $newIdentifiersByMaterialId);
            foreach ($existingFamilyIdentifiersByMaterialId as $materialId => $identifiers) {
                $identifierListStr = implode(',', array_map(function($identifier) {
                    return sprintf('"%s"', $identifier); // quote identifier
                }, $identifiers));
                $warningMessages[] = (string) __(
                    'Layouts already exist in family "%1" for material "%2": %3',
                    $origLayout->getFamilyId(),
                    $this->getMaterialIdentifier($materialId),
                    $identifierListStr);
            }

            // Collect warning messages for existing identifiers
            foreach ($existingIdentifiersByMaterialId as $materialId => $identifiers) {
                // remove family identifiers
                $identifiers = array_diff($identifiers, $familyIdentifiers);
                if (!empty($identifiers)) {
                    $identifierListStr = implode(',', array_map(function($identifier) {
                        return sprintf('"%s"', $identifier); // quote identifier
                    }, $identifiers));
                    $warningMessages[] = (string) __(
                        'Layouts already exist: %1',
                        $identifierListStr);
                }
            }

            // Add messages
            foreach ($successMessages as $message) {
                $this->messageManager->addSuccessMessage($message);
            }
            foreach ($warningMessages as $message) {
                $this->messageManager->addWarningMessage($message);
            }

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $redirect;
    }

    protected function getMaterialIdentifier($materialId)
    {
        try {
            return $this->materialRepository
                ->getById($materialId)
                ->getIdentifier();
        } catch (NoSuchEntityException $e) {
            return 'NONE';
        }
    }
}
