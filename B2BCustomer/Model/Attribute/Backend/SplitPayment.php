<?php
/*
 * @package Vinduesgrossisten
 * @author Stelmakov <stelmakov@belvg.com>
 * @Copyright
 */

namespace BelVG\B2BCustomer\Model\Attribute\Backend;

class SplitPayment extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{

    protected $request;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,

    )
    {
        $this->request = $request;
    }


    /**
     * @param \Magento\Framework\DataObject $object
     *
     * @return $this
     */
    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($this->request->__isset($attrCode)) {
            $object->setData($attrCode, $this->request->getParam($attrCode));
        }

        return $this;
    }
}
