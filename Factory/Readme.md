# Module introduced the Factory and Factory Materials for M2

-----------

### Additionals:
 * Module that check materials inside the shopping cart.

-----------

#### Tasks:
* https://youtrack.belvgdev.com/issue/SD-1581 (https://app.asana.com/0/1175739832816981/1201015559611980)
* https://youtrack.belvgdev.com/issue/SD-1589 (https://app.asana.com/0/0/1201015827326270/f)

Based on settings, module should:
* warn the customer that cart with different materials is need to be split by himself on different orders
* completely prevent purchase if cart with different materials inside

* split orders with different "Factory Family" materials


SQL for \BelVG\Factory\Model\Service\GetAllowedFactoriesBasedOnMaterials::getFactoriesMaterials:
SELECT DISTINCT `factory_material`.*, `layout_material`.`identifier` AS `material_identifier`
FROM `belvg_factory_material` AS `factory_material`
INNER JOIN `belvg_layoutmaterial_layoutmaterial` AS `layout_material`
ON factory_material.material_id = layout_material.layoutmaterial_id AND
factory_material.store_id IN (7, 0)
INNER JOIN `belvg_factory_store` AS `factory_store`
ON factory_material.factory_id = factory_store.factory_id AND
factory_store.store_id IN (SELECT MAX(store_id) FROM belvg_factory_store WHERE factory_id = factory_material.factory_id AND store_id IN (7, 0))
WHERE (factory_store.is_active = (1))
ORDER BY `factory_material`.`priority` DESC
