<?php
/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Model_Observer
{
    /**
     *
     * @param   Varien_Event_Observer $observer
     */
    public function checkExpiredOrders($observer)
    {
        $now = Mage::getModel('core/date')->timestamp(time());
        $expiredOrders = Mage::getModel("oink/order")->getCollection();
        $expiredOrders->addFieldToFilter("expiry_date", array("lt" => $now));
        foreach ($expiredOrders as $key => $order) {
            Mage::helper("oink/checkout")->cancelOrder($order);
        }
    }

    /**
     *
     * @param   Varien_Event_Observer $observer
     */
    public function removeOinkPaymentMethod($observer)
    {
        $block = $observer->getBlock();
        if ($block instanceof Mage_Checkout_Block_Onepage_Payment_Methods) {
            $block->unsetChild("payment.method." . Oink_Oink_Helper_Checkout::PAYMENT_METHOD_CODE);
            if (!Mage::helper("oink/checkout")->isEnabled()) {
                $methods = $block->getMethods();
                foreach ($methods as $key => $method) {
                    if ($method->getCode() == Oink_Oink_Helper_Checkout::PAYMENT_METHOD_CODE) {
                        unset($methods[$key]);
                    }
                }
                $block->setData('methods', $methods);
            }
        }
    }

    /**
     * TODO change this to invoice PRE paid
     *
     * @param Varien_Event_Observer $observer
     */
    public function adminOrderStatusChange($observer)
    {
    	$result = '';
        $order = $observer->getEvent()->getOrder();
        $state = $observer->getEvent()->getState();

        if (Mage::helper("oink/checkout")->isTwoStepsAuthorizationEnabled()) {
            $vpOrder = Mage::helper("oink/checkout")->getOinkOrder($order->getId());
            $transactionIdentifier = $vpOrder->getTransactionIdentifier();

            /**
             * @var OinkPaymentService $paymentService
             */
            $paymentService = Mage::helper("oink")->getOinkPaymentService();

            if ($transactionIdentifier) {
                if ($state == Mage_Sales_Model_Order::STATE_CANCELED) {
                    $result = $paymentService->VoidCaptureTransactionByIdentifier($transactionIdentifier);
                }

                if ($result) {
                    Mage::helper("oink")->log($result, "resultOfProcessTwoSteps");
                    if (!(bool)$result->Status) {
                        Mage::getSingleton("core/session")->addError($result->ErrorMessage);
                    }
                }
            }
        }
    }

}
