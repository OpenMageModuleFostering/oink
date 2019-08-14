<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 3/10/14
 * Time: 10:53 AM
 */


/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Model_Sales_Order_Payment
    extends Mage_Sales_Model_Order_Payment {


    /**
     * Over-riding this function, so we can not set the payment status to processing when is two step
     * @param float $amount
     * @return $this|Mage_Sales_Model_Order_Payment
     */
    protected function _order($amount)
    {
        $instance = $this->getMethodInstance();
        if($instance instanceof Oink_Oink_Model_Payment_Method_Oink) {
            return $this;
        }
        else {
            return parent::_order($amount);
        }
    }


    public function place()
    {
        $instance = $this->getMethodInstance();
        if($instance instanceof Oink_Oink_Model_Payment_Method_Oink) {
            Mage::dispatchEvent('sales_order_payment_place_start', array('payment' => $this));

            $order = $this->getOrder();

            $this->setAmountOrdered($order->getTotalDue());
            $this->setBaseAmountOrdered($order->getBaseTotalDue());
            $this->setShippingAmount($order->getShippingAmount());
            $this->setBaseShippingAmount($order->getBaseShippingAmount());

            $methodInstance = $this->getMethodInstance();
            $methodInstance->setStore($order->getStoreId());

            $stateObject = new Varien_Object();

            //Do order payment validation on payment method level

            $methodInstance->validate();
            $action = $methodInstance->getConfigPaymentAction();
            if ($action) {
                if ($methodInstance->isInitializeNeeded()) {
                    //For method initialization we have to use original config value for payment action
                    $methodInstance->initialize($methodInstance->getConfigData('payment_action'), $stateObject);
                } else {
                    switch ($action) {
                        case Mage_Payment_Model_Method_Abstract::ACTION_ORDER:
                            $this->_order($order->getBaseTotalDue());
                            break;
                        case Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE:
                            $this->_authorize(true, $order->getBaseTotalDue()); // base amount will be set inside
                            $this->setAmountAuthorized($order->getTotalDue());
                            break;
                        case Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE:
                            $this->setAmountAuthorized($order->getTotalDue());
                            $this->setBaseAmountAuthorized($order->getBaseTotalDue());
                            $this->capture(null);
                            break;
                        default:
                            break;
                    }
                }
            }

            $this->_createBillingAgreement();

            Mage::dispatchEvent('sales_order_payment_place_end', array('payment' => $this));
            return $this;
        }
        else {
            return parent::place();
        }


    }
} 