<?php
namespace BelVG\Factory\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface FactoryInterface extends ExtensibleDataInterface
{
    const FACTORY_ID            = 'factory_id';
    const IS_ACTIVE             = 'is_active';
    const NAME                  = 'name';
    const IDENTIFIER            = 'identifier';
    const EMAIL                 = 'email';
    const string CALCULATION_TYPE = 'calculation_type';
    const DEFAULT_DELIVERY_TIME = 'default_delivery_time';
    const DELIVERY_WEEKS_INTERVAL = 'delivery_weeks_interval';
    const DELIVERY_WEEKS_INTERVAL_FRONTEND = 'delivery_weeks_interval_frontend';
    const FACTORY_DELIVERY_TRANSPORT_TIME = 'factory_delivery_transport_time';
    const FACTORY_DELIVERY_WEEKS_INTERVAL = 'factory_delivery_weeks_interval';
    const EMAIL_TEMPLATE        = 'email_template';

    /**
     * @return int
     */
    public function getFactoryId();

    /**
     * @param int $factoryId
     * @return $this
     */
    public function setFactoryId($factoryId);

    /**
     * @return bool
     */
    public function getIsActive();

    /**
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @param string $identifier
     * @return $this
     */
    public function setIdentifier($identifier);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

    /**
     * @return int
     */
    public function getCalculationType();

    /**
     * @param int|string|null $calculationType
     * @return $this
     */
    public function setCalculationType($calculationType);

    /**
     * @return string
     */
    public function getDefaultDeliveryTime();

    /**
     * @param string $defaultDeliveryTime
     * @return $this
     */
    public function setDefaultDeliveryTime($defaultDeliveryTime);

    /**
     * @return int
     */
    public function getDeliveryWeeksInterval();

    /**
     * @param int|string|null $deliveryWeeksInterval
     * @return $this
     */
    public function setDeliveryWeeksInterval(int|string|null $deliveryWeeksInterval);

    /**
     * @return int
     */
    public function getDeliveryWeeksIntervalFrontend();

    /**
     * @param int|string|null $deliveryWeeksIntervalFrontend
     * @return $this
     */
    public function setDeliveryWeeksIntervalFrontend(int|string|null $deliveryWeeksIntervalFrontend);

    /**
     * @return int
     */
    public function getFactoryDeliveryTransportTime();

    /**
     * @param int|string|null $factoryDeliveryTransportTime
     * @return $this
     */
    public function setFactoryDeliveryTransportTime(int|string|null $factoryDeliveryTransportTime);

    /**
     * @return int
     */
    public function getFactoryDeliveryWeeksInterval();

    /**
     * @param int|string|null $factoryDeliveryWeeksInterval
     * @return $this
     */
    public function setFactoryDeliveryWeeksInterval(int|string|null $factoryDeliveryWeeksInterval);

    /**
     * @return string
     */
    public function getEmailTemplate();

    /**
     * @param string $emailTemplate
     * @return $this
     */
    public function setEmailTemplate($emailTemplate);

    /**
     * @return \BelVG\Factory\Api\Data\FactoryExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * @param \BelVG\Factory\Api\Data\FactoryExtensionInterface
     * @return $this
     */
    public function setExtensionAttributes(
        \BelVG\Factory\Api\Data\FactoryExtensionInterface $extensionAttributes);
}
