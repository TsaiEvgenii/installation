<?php
/*
 * ~ @package Vinduesgrossisten.
 * ~ @author Tsai Eugene <tsai.evgenii@gmail.com>
 * ~ Copyright (c) 2023.
 */
declare(strict_types=1);


namespace BelVG\MageWorxOptionMadeInDenmark\Plugin\Model\OptionSaver;


use BelVG\MadeInDenmark\Model\Service\DenmarkPreferred\ProductEstimatorService;
use BelVG\MageWorxOptionTemplates\Model\OptionSaver;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

class ReplacePriceWithMadeInDenmarkPrice
{
    private const LOGGER_PREFIX = 'BelVG_MageWorxOptionMadeInDenmark::ReplacePriceWithMadeInDenmarkPricePlugin: ';
    public function __construct(
        protected ProductEstimatorService $productEstimatorService,
        protected SerializerInterface $serializer,
        protected LoggerInterface $logger
    ){

    }
    public function afterGetPreparedProductOptions(
        OptionSaver $subject,
        array $result,
        Product $product,
        string $saveMode
    ): array {
        try {
            if (!$this->productEstimatorService->isMadeInDenmark($product)) {
                return $result;
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->error(self::LOGGER_PREFIX . $e->getMessage());
            return $result;
        }
        foreach ($result as $optionId => $option){
            foreach (($option['values'] ?? []) as $valueId => $value){
                if(($value['made_in_denmark_price'] ?? false) && $value['made_in_denmark_price'] !== '0.0000'){
                    $result[$optionId]['values'][$valueId]['price'] = $value['made_in_denmark_price'];
                    $mageworxOptionTypePrice = $this->serializer->unserialize($value['mageworx_option_type_price']);
                    $mageworxOptionTypeMadeInDenmarkPrice = $this->serializer->unserialize($value['mageworx_option_type_made_in_denmark_price']);
                    foreach ($mageworxOptionTypeMadeInDenmarkPrice as $madeInDenmarkPriceOption){
                        foreach ($mageworxOptionTypePrice as &$priceOption){
                            if($madeInDenmarkPriceOption['store_id'] === $priceOption['store_id']){
                                $priceOption['price'] = $madeInDenmarkPriceOption['price'];
                            }
                        }
                    }
                    $result[$optionId]['values'][$valueId]['mageworx_option_type_price'] = $this->serializer->serialize($mageworxOptionTypePrice);
                }
            }
        }

        return $result;
    }
}