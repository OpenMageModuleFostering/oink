<?php
/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Model_Payment_Method_Oink
    extends Mage_Payment_Model_Method_Abstract
{
    /**
     *
     * @var string
     */
    protected $_code = 'oink';
    protected $_canOrder = true;
    protected $_formBlockType = 'oink/payment_form_oink';
    protected $_infoBlockType = 'oink/payment_info_oink';
    protected $_canCapture = true;

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        Mage::log("Oink Payment info: \n" . print_r($data, true));

        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }

        Mage::log("Oink data: \n" . print_r($data, true));

        if ($data->getBtToken()) {
            $data->setPoNumber($data->getBtToken());
        }
        if ($data->getBtCustomerId()) {
            $data->setCcOwner($data->getBtCustomerId());
        }
        if ($data->getPoNumber()) {
            $data->setBtToken($data->getPoNumber());
        }
        if ($data->getCcOwner()) {
            $data->setBtCustomerId($data->getCcOwner());
        }

        $this->getInfoInstance()->addData($data->getData());
        return $this;
    }

    /**
     * Order
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function order(Varien_Object $payment, $amount)
    {

        $payment->setTransactionId(
            $this->getInfoInstance()->getBtTransactionId()
        );

        return $this;
    }

    /**
     * Capture payment abstract method
     *
     * @param Varien_Object $payment
     * @param float $amount
     *
     * @return Mage_Payment_Model_Abstract|Oink_Oink_Model_Payment_Method_Oink
     */
    public function capture(Varien_Object $payment, $amount)
    {
        parent::capture($payment, $amount);

        /**
         * @var Oink_Oink_Helper_Checkout $vpHelper
         */
        $vpHelper = Mage::helper("oink/checkout");

        if ($vpHelper->isTwoStepsAuthorizationEnabled()) {
            $this->captureTwoStepOrder($payment, $amount);
            return $this;
        }


        return $this;
    }

    /**
     * @return bool
     */
    public function getIsTransactionPending()
    {
        /**
         * @var Oink_Oink_Helper_Checkout $vpHelper
         */
        $vpHelper = Mage::helper("oink/checkout");

        if ($vpHelper->isTwoStepsAuthorizationEnabled()) {
            return true;
        }

        return parent::getIsTransactionPending();
    }

    /**
     * Check capture availability
     *
     * @return bool
     */
    public function canCapture()
    {
        $order = $this->getInfoInstance()->getOrder();

        if (!$this->_canCapture) {
            return false;
        }


        if ($order) {
            if ($order->getOinkStatus() == Oink_Oink_Helper_Checkout::ORDER_STATUS_APPROVED) {
                return true;
            } else {
                //Mage::getSingleton('adminhtml/session')->addWarning('This order is not approved, so can not be captured');
                return false;
            }
        }

        return false;
    }

    private function captureTwoStepOrder(Varien_Object $payment, $amount)
    {
        /**
         * @var OinkPaymentService $paymentService
         */
        $paymentService = Mage::helper("oink")->getOinkPaymentService();
        $order = $payment->getOrder();

        if (!$order) {
            Mage::throwException(Mage::helper('paygate')->__('Error in capturing the payment.'));
        }

        $vpOrder = Mage::helper("oink/checkout")->getOinkOrder($order->getId());
        $transactionIdentifier = $vpOrder->getTransactionIdentifier();

        if (!$transactionIdentifier) {
            Mage::throwException(Mage::helper('paygate')->__('Error in capturing the payment.'));
        }

        $result = $paymentService->CaptureTransactionByIdentifier($transactionIdentifier);

        if (!$result->Status) {
            Mage::throwException(Mage::helper('paygate')->__($result->ErrorMessage));
        }

        return $this;
    }
}
