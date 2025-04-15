<?php
namespace BelVG\Factory\Plugin;

use Magento\Framework\App\RequestInterface;
use BelVG\Factory\Plugin\FactoryRepositoryMaterial\AfterSave\ActionsPool;
use BelVG\Factory\Plugin\FactoryRepositoryMaterial\AfterGetById\FrontNamesPool\FrontNamesPool;
use BelVG\Factory\Api\Data\FactoryInterface;
use BelVG\Factory\Api\FactoryMaterialRepositoryInterface;
use BelVG\Factory\Model\FactoryRepository as Subject;

class FactoryRepositoryMaterial
{
    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;
    /**
     * @var FactoryMaterialRepositoryInterface
     */
    private FactoryMaterialRepositoryInterface $factoryMaterialRepo;

    /**
     * @var ActionsPool
     */
    private ActionsPool $actionsPool;

    /**
     * @var FrontNamesPool
     */
    protected FrontNamesPool $frontNamesPool;

    /**
     * @param FactoryMaterialRepositoryInterface $factoryMaterialRepo
     * @param ActionsPool $actionsPool
     * @param FrontNamesPool $frontNamesPool
     */
    public function __construct(
        RequestInterface $request,
        FactoryMaterialRepositoryInterface $factoryMaterialRepo,
        ActionsPool $actionsPool,
        FrontNamesPool $frontNamesPool
    ) {
        $this->request = $request;
        $this->factoryMaterialRepo = $factoryMaterialRepo;
        $this->actionsPool = $actionsPool;
        $this->frontNamesPool = $frontNamesPool;
    }

    public function afterGetById(Subject $repo, FactoryInterface $factory, $factoryId, $storeId = null)
    {
        $this->factoryMaterialRepo->setStoreId($storeId);
        $list = $this->factoryMaterialRepo->getListByFactory($factory, $this->isShouldCheckActivity());

        $extensionAttributes = $factory->getExtensionAttributes();
        $extensionAttributes->setMaterials($list->getItems());
        $factory->setExtensionAttributes($extensionAttributes);

        return $factory;
    }

    public function afterSave(Subject $repo, $result, FactoryInterface $factory, $storeId = null)
    {
        $factory->setFactoryId($result->getFactoryId());
        /** @var \BelVG\Factory\Api\FactoryRepositoryMaterial\AfterSave\ActionInterface $action */
        foreach ($this->actionsPool->getActions() as $action) {
            $action->execute($factory, (int)$storeId);
        }

        return $result;
    }

    /**
     * @return bool
     */
    protected function isShouldCheckActivity(): bool
    {
        $shouldCheckActivity = true;
        $requestFrontName = $this->request->getRouteName();
        foreach ($this->frontNamesPool->getFrontNames() as $frontName) {
            if ($frontName === $requestFrontName) {
                $shouldCheckActivity = false;
                break;
            }
        }
        unset($frontName);

        return $shouldCheckActivity;
    }
}
