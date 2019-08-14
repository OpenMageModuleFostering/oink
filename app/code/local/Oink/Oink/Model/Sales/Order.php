<?php

/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Model_Sales_Order
        extends Mage_Sales_Model_Order
{
    /**
     * Overrides the base _setState but also dispatching a new Event
     * @param string $state
     * @param bool $status
     * @param string $comment
     * @param null $isCustomerNotified
     * @return Mage_Sales_Model_Order
     */

    public function _setState($state, $status = false, $comment = '', $isCustomerNotified = null, $shouldProtectState = false)
    {
        Mage::dispatchEvent('sales_order_status_change', array('order' => $this, 'state' => $state, 'status' => $status, 'comment' => $comment, 'isCustomerNotified' => $isCustomerNotified));

        return parent::_setState($state, $status, $comment, $isCustomerNotified);
    }

}
