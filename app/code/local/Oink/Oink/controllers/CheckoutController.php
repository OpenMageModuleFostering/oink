<?php

/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_CheckoutController extends Mage_Core_Controller_Front_Action
{

    /**
     * Confirmation checkout page
     */
    public function indexAction()
    {
        if (!Mage::helper("oink/checkout")->customerHasProductsInCart()) {
            Mage::getSingleton("core/session")->addError($this->__("You need to have products in your cart."));
            $this->_redirect("checkout/cart/index");
        } elseif (!Mage::helper("oink")->isUserLogged()) {
            Mage::getSingleton("core/session")->addError($this->__("You need to be logged in Oink."));
            $this->_redirect("checkout/cart/index");
        } else {
            if ($this->_isOrderReadyForConfirmation()) {
                $this->_redirect("oink/checkout/parentConfirm");
            }
            else if (!($this->_isShippingMethodSelected())){
                $this->_setAddress();
                $this->_redirect("oink/checkout/shippingMethod");
            } else {
                Mage::helper("oink/checkout")->populateQuote();
                $this->loadLayout()
                    ->renderLayout();
            }
        }
    }

    /**
    * Parent Confirmation page
    */
    public function parentConfirmAction()
    {
        Mage::getSingleton("customer/session")->unsParentConfirm();
        try {
            $this->loadLayout()->renderLayout();
        } catch (Exception $e) {
            $errorMessage = Mage::getSingleton("oink/errorHandler")->rewriteError($e->getMessage());
            Mage::getSingleton("core/session")->addError($errorMessage);
            $this->_redirect("checkout/cart/index");
        }
    }

    /**
     * Shipping method page
     */
    public function shippingMethodAction()
    {
        try {
            $this->loadLayout()->renderLayout();

        } catch (Exception $e) {
            $errorMessage = Mage::getSingleton("oink/errorHandler")->rewriteError($e->getMessage());
            Mage::getSingleton("core/session")->addError($errorMessage);
            $this->_redirect("checkout/cart/index");
        }
    }

    /**
     * Login Oink user page
     */
    public function loginPostAction()
    {
        Mage::getSingleton("customer/session")->unsParentConfirm();
        $user = $this->getRequest()->getPost("user");
        $password = $this->getRequest()->getPost("password");
        $loginResponse = array();
        try {
            $loginResponse["response"] = (bool)Mage::helper("oink")->authenticateUser($user, $password);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), "temporarily disabled") !== false) {
                $loginResponse["errorMessage"] = Mage::getStoreConfig("oink/messages/max_login_attemps");
            } else {
                $loginResponse["errorMessage"] = Mage::getStoreConfig("oink/messages/login_error");
            }
        }
        /*
           * Zend_Json_Encoder is required for magento to work. This will always be available in magento installations.
           * */
        $this->getResponse()->setBody(Zend_Json_Encoder::encode($loginResponse));
    }

    /**
     * Process order page
     */
    public function placeOrderAction()
    {

        /**
         * @var Oink_Oink_Helper_Checkout $vpCheckoutHelper
         */
        $vpCheckoutHelper = Mage::helper("oink/checkout");

        try {
            $cart = $vpCheckoutHelper->getOinkCart();
            $result = $vpCheckoutHelper->sendCartToOink($cart);

            Mage::helper("oink")->log($result, "resultOfProcessTransaction");
            if ((bool)$result->Status) {
                $this->_placeOrder();
                $order = $vpCheckoutHelper->getOinkOrder();
                $order->setOrderId($this->getCheckout()->getLastOrderId());
                $createdAt = strtotime($vpCheckoutHelper->getOrder()->getCreatedAt());
                $expiryDate = $createdAt + Mage::helper("oink")->getExpiryTime();

                $order->setExpiryDate($expiryDate);
                $order->setTransactionIdentifier($result->TransactionIdentifier);
                $order->save();

                $this->getCheckout()->clear();

                $originalOrder = Mage::getModel('sales/order')->load($order->getOrderId());

                if ($result->TransactionStatus == Oink_Oink_Helper_Checkout::APPROVAL_PENDING_CODE) {
                    $message = Mage::getStoreConfig("oink/messages/approval_required");
                    $originalOrder->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true, 'Awaiting parent approval - TXID: '.$result->TransactionIdentifier);

                    $originalOrder->setOinkStatus(
                        Oink_Oink_Helper_Checkout::ORDER_STATUS_APPROVAL_PENDING
                    );
                } else {
                    $message = Mage::getStoreConfig("oink/messages/success_transaction");
                    $vpCheckoutHelper->completeOrder($order);
                    $originalOrder->sendNewOrderEmail();

                    $originalOrder->setOinkStatus(
                        Oink_Oink_Helper_Checkout::ORDER_STATUS_APPROVED
                    );

                    $originalOrder->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'TXID: '.$result->TransactionIdentifier);
                }

                $order->save();
                $originalOrder->save();

                Mage::getSingleton("core/session")->addSuccess($message);
                $path = "*/*/success";
                Mage::getSingleton("customer/session")->unsParentConfirm();
            } else {
                $errorMessage = Mage::getSingleton("oink/errorHandler")->rewriteError($result->ErrorMessage);
                Mage::getSingleton("core/session")->addError($errorMessage);
                $path = "*/*/index";
            }
        } catch (Exception $e) {
            Mage::getSingleton("core/session")->addError($e->getMessage());
            $path = "*/*/index";
        }

        $this->_redirect($path);
    }

    /*
    * @return bool
    */
    protected function _isOrderReadyForConfirmation()
    {
        return Mage::helper("oink")->getUserType() == Oink_Oink_Model_User::USER_CODE_TYPE_PARENT
        && !(bool)Mage::getSingleton("customer/session")->getParentConfirm();
    }

    protected function _setAddress()
    {
        $address = Mage::helper('oink/checkout')->getUser()->getAddress(null,true);

        Mage::getModel('checkout/type_onepage')->saveShipping($address->getData());
    }

    protected function _isShippingMethodSelected()
    {
        $shippingMethod = Mage::helper('oink/checkout')->getUser()->getData('shipping_method');
        return isset($shippingMethod);
    }

    protected function _placeOrder()
    {
        $quote = $this->_prepareGuestQuote();

        $service = Mage::getModel('sales/service_quote', $quote);
        $service->submitAll();
        $checkoutSession = $this->getCheckout();

        $checkoutSession->setLastQuoteId($quote->getId())
            ->setLastSuccessQuoteId($quote->getId())
            ->clearHelperData();

        $order = $service->getOrder();
        $redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();

        $checkoutSession->setLastOrderId($order->getId())
            ->setRedirectUrl($redirectUrl)
            ->setLastRealOrderId($order->getIncrementId());

        $agreement = $order->getPayment()->getBillingAgreement();
        if ($agreement) {
            $checkoutSession->setLastBillingAgreementId($agreement->getId());
        }

        // add recurring profiles information to the session
        $profiles = $service->getRecurringPaymentProfiles();
        if ($profiles) {
            $ids = array();
            foreach ($profiles as $profile) {
                $ids[] = $profile->getId();
            }
            $checkoutSession->setLastRecurringProfileIds($ids);
            // TODO: send recurring profile emails
        }

        Mage::dispatchEvent(
            'checkout_submit_all_after', array('order' => $order, 'quote' => $this->getQuote(), 'recurring_profiles' => $profiles)
        );
    }

    /**
     * Callback page
     */
    public function callbackAction()
    {
        $params = $this->getRequest()->getParams();
        $transactionId = $params["TransactionIdentifier"];
        if(!isset($transactionId) && isset($params['id']))
            $transactionId = $params['id'];
        Mage::helper("oink")->log($params, "receivedCallbackMessage");
        if ($transactionId) {
            /**
             * @var Oink_Oink_Helper_Checkout $checkoutHelper
             */
            $checkoutHelper = Mage::helper("oink/checkout");

            $order = $checkoutHelper->getOinkOrder($transactionId, "transaction_identifier");
            if ($order->getId()) {
                $order->addAdditionalInformation("parentApproval", $params);

                $originalOrder = Mage::getModel('sales/order')->load($order->getOrderId());

                if(isset($params['Status']) && $params['Status'] == 'Rejected') {
                    $originalOrder->setData(
                        'oink_status',
                        Oink_Oink_Helper_Checkout::ORDER_STATUS_REJECTED
                    );
                    $originalOrder->setState(Mage_Sales_Model_Order::STATE_CANCELED, Mage_Sales_Model_Order::STATE_CANCELED,
                        $this->__('This transaction was rejected by the parent'))->save();

                    if($originalOrder->canCancel()) {
                        $originalOrder->cancel()->save();
                    }
                }
                else if(isset($params['Status']) && $params['Status'] == 'Processed') {
                    $originalOrder->setData(
                        'oink_status',
                        Oink_Oink_Helper_Checkout::ORDER_STATUS_APPROVED
                    );
                    $originalOrder->setState(Mage_Sales_Model_Order::STATE_PROCESSING, Mage_Sales_Model_Order::STATE_PROCESSING,
                        $this->__('This transaction was approved by the parent'))->save();
                    $originalOrder->sendNewOrderEmail();
                }

                $originalOrder->save();

                $checkoutHelper->completeOrder($order);
            }
        }
    }

    /**
     * Checkout success page
     */
    public function successAction()
    {
        $this->loadLayout()
            ->renderLayout();
    }

    /*
    * Quick Connect page
    */
    public function quickconnecetAction()
    {

    }

    /**
     * Process Parent Confirmation page
     */
    public function processParentConfirmAction()
    {
        $params = $this->getRequest()->getParams();
        $errors = array();
        if (!isset ($params["children"])) {
            $errors[] = $this->__("You need to select one children");
        }
        if (!isset ($params["paymentAccount"])) {
            $errors[] = $this->__("You need to select one payment account");
        }
        if ((bool)count($errors)) {
            foreach ($errors as $error) {
                Mage::getSingleton("core/session")->addError($error);
            }
            $this->_redirect("*/*/parentConfirm");
        } else {
            Mage::helper("oink")->getUser()->addData(array(
                "selected_children" => $params["children"],
                "selected_payment_account" => $params["paymentAccount"],
                "deliver_to_children" => isset ($params["deliverToChildAddress"]),
                "notify_children" => isset ($params["notifyChild"]),
            ));
            Mage::getSingleton("customer/session")->setParentConfirm(true);
            $this->_redirect("*/*/index");
        }
    }
    /**
     * Process Shipping Method page
     */
    public function processShippingMethodAction()
    {
        $params = $this->getRequest()->getParams();
        $errors = array();
        if (!isset ($params["shipping_method"])) {
            $errors[] = $this->__("You need to select a shipping method");
        }
        if ((bool)count($errors)) {
            foreach ($errors as $error) {
                Mage::getSingleton("core/session")->addError($error);
            }
            $this->_redirect("*/*/shippingMethod");
        } else {
            Mage::helper("oink")->getUser()->addData(array(
                "shipping_method" => $params["shipping_method"],
            ));
            $this->_redirect("*/*/index");
        }
    }
    /**
     * Prepare quote for guest checkout order submit
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    protected function _prepareGuestQuote()
    {
        $quote = $this->getQuote();
        $quote->setCustomerId(null)
            ->setCustomerEmail($quote->getBillingAddress()->getEmail())
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);

        $quote->setTotalsCollectedFlag(true);
        ;

        return $quote;
    }

    /*
    *
    * @return Mage_Checkout_Model_Session
    */

    public function getCheckout()
    {
        return Mage::getSingleton("checkout/session");
    }

    /*
     *
     * @return Mage_Sales_Model_Quote
     */

    public function getQuote()
    {
        return Mage::helper("oink/checkout")->getQuote();
    }

}
