<?php
/**
 * SaveTemplateTest
 * @package BelVG
 * @author    artsem.belvg@gmai.com
 * @copyright Copyright © 2012 - 2020
 */

namespace BelVG\MageWorxOptionTemplates\Test\Integration\Controller\Adminhtml;


use Magento\Catalog\Model\ProductRepository;

class SaveTemplateTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    public function testSaveDefaultStore()
    {
        $params = include __DIR__.'/../__files/request_to_save_template_1.php';
        $this->getRequest()->setParams($params);
        $this->dispatch('backend/mageworx_optiontemplates/group/save/store/0');
        $this->assertEmpty($this->getResponse()->getBody());
        /**
         * @var \Magento\Framework\MessageQueue\ConsumerFactory $consumerFactory
         */
        $consumerFactory = $this->_objectManager->get(\Magento\Framework\MessageQueue\ConsumerFactory::class);
        $consumer = $consumerFactory->get('option_templates.save',100);
        $consumer->process(10000);
        $productId = 4883;
        $value = $this->getOptionValue($productId);
        $this->assertSame('Left outward, Fixed, Right outward', $value->getData('default_title'));
        $this->assertSame('Left outward, Fixed, Right outward', $value->getData('store_title'));

    }


    public function testSaveAnotherStore()
    {

        $params = include __DIR__.'/../__files/request_to_save_template_store_id_7.php';
        $this->getRequest()->setParams($params);
        $this->dispatch('backend/mageworx_optiontemplates/group/save/store/7');
        $this->assertEmpty($this->getResponse()->getBody());
        /**
         * @var \Magento\Framework\MessageQueue\ConsumerFactory $consumerFactory
         */
        $consumerFactory = $this->_objectManager->get(\Magento\Framework\MessageQueue\ConsumerFactory::class);
        $consumer = $consumerFactory->get('option_templates.save',100);
        $consumer->process(10000);
        $productId = 4883;
        $value = $this->getOptionValue($productId);
        $this->assertSame('Left outward, Fixed, Right outward', $value->getData('default_title'));
        $this->assertSame('Left outward, Fixed, Right outward', $value->getData('store_title'));
        //for 7 store
        $value = $this->getOptionValue($productId, 7);
        $this->assertSame('Left outward, Fixed, Right outward', $value->getData('default_title'));
        $this->assertSame('Test test', $value->getData('store_title'));
    }

    private function getOptionValue(int $productId, $storeId = 0,$optionName='Opening direction (bottom)')
    {
        /**
         * @var ProductRepository $productRepository
         */
        $productRepository  = $this->_objectManager->get(ProductRepository::class);
        /**
         * for default data
         */
        $product = $productRepository->getById($productId, false, $storeId);
        $mageWorxOptions = $product->getOptions();
        $data = null;
        foreach ($mageWorxOptions as $mageWorxOption){
            if(trim($mageWorxOption->getData('default_title')) === $optionName){
                $data = $mageWorxOption;
            }

        }
        $this->assertInstanceOf(\Magento\Catalog\Model\Product\Option::class, $data);
        $values = $data->getValues();
        //check for default store, it is shouldn't be changed
        $firstValue = \reset($values);
        return $firstValue;
    }

}