<?php
namespace BelVG\Factory\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use BelVG\Factory\Api\Data\FactoryInterface;

class Factory extends AbstractExtensibleObject implements FactoryInterface
{
    public function getFactoryId()
    {
        return $this->_get(self::FACTORY_ID);
    }

    public function setFactoryId($factoryId)
    {
        return $this->setData(self::FACTORY_ID, $factoryId);
    }

    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }

    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    public function getName()
    {
        return $this->_get(self::NAME);
    }

    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    public function getIdentifier()
    {
        return $this->_get(self::IDENTIFIER);
    }

    public function setIdentifier($identifier)
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    public function getEmail()
    {
        return $this->_get(self::EMAIL);
    }

    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @inheritDoc
     */
    public function getCalculationType()
    {
        return is_numeric($this->_get(self::CALCULATION_TYPE)) ? (int) $this->_get(self::CALCULATION_TYPE) : null;
    }

    /**
     * @inheritDoc
     */
    public function setCalculationType($calculationType)
    {
        return $this->setData(self::CALCULATION_TYPE, $calculationType);
    }

    public function getDefaultDeliveryTime()
    {
        return $this->_get(self::DEFAULT_DELIVERY_TIME);
    }

    public function setDefaultDeliveryTime($defaultDeliveryTime)
    {
        return $this->setData(self::DEFAULT_DELIVERY_TIME, $defaultDeliveryTime);
    }

    /**
     * @inheritdoc
     */
    public function getDeliveryWeeksInterval()
    {
        return (int) $this->_get(self::DELIVERY_WEEKS_INTERVAL);
    }

    /**
     * @inheritdoc
     */
    public function setDeliveryWeeksInterval(int|string|null $deliveryWeeksInterval): static
    {
        return $this->setData(self::DELIVERY_WEEKS_INTERVAL, $deliveryWeeksInterval);
    }

    /**
     * @inheritdoc
     */
    public function getDeliveryWeeksIntervalFrontend()
    {
        return (int) $this->_get(self::DELIVERY_WEEKS_INTERVAL_FRONTEND);
    }

    /**
     * @inheritdoc
     */
    public function setDeliveryWeeksIntervalFrontend(int|string|null $deliveryWeeksIntervalFrontend): static
    {
        return $this->setData(self::DELIVERY_WEEKS_INTERVAL_FRONTEND, $deliveryWeeksIntervalFrontend);
    }

    /**
     * @inheritdoc
     */
    public function getFactoryDeliveryTransportTime()
    {
        return (int) $this->_get(self::FACTORY_DELIVERY_TRANSPORT_TIME);
    }

    /**
     * @inheritdoc
     */
    public function setFactoryDeliveryTransportTime(int|string|null $factoryDeliveryTransportTime): static
    {
        return $this->setData(self::FACTORY_DELIVERY_TRANSPORT_TIME, $factoryDeliveryTransportTime);
    }

    /**
     * @inheritdoc
     */
    public function getFactoryDeliveryWeeksInterval()
    {
        return (int) $this->_get(self::FACTORY_DELIVERY_WEEKS_INTERVAL);
    }

    /**
     * @inheritdoc
     */
    public function setFactoryDeliveryWeeksInterval(int|string|null $factoryDeliveryWeeksInterval): static
    {
        return $this->setData(self::FACTORY_DELIVERY_WEEKS_INTERVAL, $factoryDeliveryWeeksInterval);
    }

    public function getEmailTemplate()
    {
        return $this->_get(self::EMAIL_TEMPLATE);
    }

    public function setEmailTemplate($emailTemplate)
    {
        return $this->setData(self::EMAIL_TEMPLATE, $emailTemplate);
    }


    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    public function setExtensionAttributes(
        \BelVG\Factory\Api\Data\FactoryExtensionInterface $extensionAttributes
    ) {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
