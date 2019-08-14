<?php

/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Helper_Checkout
    extends Mage_Core_Helper_Abstract
{
    /**
     * Code of the payment method
     *
     * @var string
     */
    const PAYMENT_METHOD_CODE = "oink";
    /**
     * Approval pending code
     *
     * @var string
     */
    const APPROVAL_PENDING_CODE = "ApprovalPending";
    /**
     * Approval pending code
     *
     * @var string
     */
    const ADDITIONAL_DATA_INDEX = "oink_data";
    /**
     * Enabled code for All Users
     *
     * @var string
     */
    const ENABLE_CODE_ALL_USERS = "1";
    /**
     * Enabled code for All Registered Users
     *
     * @var string
     */
    const ENABLE_CODE_REGISTERED_USERS = "2";
    /**
     * Enabled code for All Guest Users
     *
     * @var string
     */
    const ENABLE_CODE_GUEST_USERS = "3";
    /**
     * Enabled code for All Registered Users
     *
     * @var string
     */
    const REGISTERED_USER_CHECKOUT_METHOD = "registered";

    const ORDER_STATUS_APPROVAL_PENDING = 0;
    const ORDER_STATUS_APPROVED = 1;
    const ORDER_STATUS_REJECTED = -1;

    protected $_order;
    protected $_oinkOrder;

    /**
     * Check if customer have products in cart
     * @return bool
     */
    public function customerHasProductsInCart()
    {
        $quote = $this->getQuote();
        return (bool)$quote->getItemsCount();
    }

    /**
     * @return bool
     */
    public function isCheckoutWithRegistered()
    {
        $quoteCheckoutMethod = $this->getQuote()->getCheckoutMethod();
        return (
            $quoteCheckoutMethod == Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER
            ||
            $quoteCheckoutMethod == Mage_Sales_Model_Quote::CHECKOUT_METHOD_LOGIN_IN
        );
    }

    /**
     * @return bool
     */
    public function isCheckoutWithGuest()
    {
        $quoteCheckoutMethod = $this->getQuote()->getCheckoutMethod();
        return (
            $quoteCheckoutMethod != Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER
            &&
            $quoteCheckoutMethod != Mage_Sales_Model_Quote::CHECKOUT_METHOD_LOGIN_IN
        );
    }


    public function isEnabled()
    {
        $config = Mage::getStoreConfig("oink/checkoutbutton/show_payment_method");
        $enabled = (
            ($config == self::ENABLE_CODE_ALL_USERS)
            ||
            ($config == self::ENABLE_CODE_REGISTERED_USERS
                && $this->isCheckoutWithRegistered()
            ) || ($config == self::ENABLE_CODE_GUEST_USERS
                && $this->isCheckoutWithGuest())
        );

        return $enabled;
    }

    public function isTwoStepsAuthorizationEnabled()
    {
        $configShow = Mage::getStoreConfig("oink/checkoutbutton/show_payment_method");
        $configTwoSteps = Mage::getStoreConfig("oink/merchant_info/two_steps_authorization");

        $enabled = (
            (
                ($configShow = self::ENABLE_CODE_ALL_USERS)
                ||
                ($configShow == self::ENABLE_CODE_REGISTERED_USERS)
                ||
                ($configShow == self::ENABLE_CODE_GUEST_USERS)
            )
            &&
            ($configTwoSteps == 1)
        );

        return $enabled;
    }


    /**
     * Populate the quote with addresses and totals
     */
    public function populateQuote()
    {
        $this->_createAddresses();
        $this->getQuote()->collectTotals();
        Mage::getSingleton('checkout/type_onepage')->savePayment(array(
            "method" => self::PAYMENT_METHOD_CODE
        ));
        $this->getQuote()->save();
    }

    /**
     * Get checkout quote instance by current session
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /*
     * 
     * @return Mage_Checkout_Model_Session
     */

    public function getCheckout()
    {
        return Mage::getSingleton("checkout/session");
    }

    /**
     * Get Oink user
     *
     * @return Oink_Oink_Model_User
     */
    public function getUser()
    {
        return Mage::helper("oink")->getUser();
    }

    /**
     * Get checkout quote instance by current session
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getOrder()
    {
        if (is_null($this->_order)) {
            $orderId = $this->getCheckout()->getLastOrderId();
            $this->_order = Mage::getModel("sales/order")->load($orderId);
        }
        return $this->_order;
    }

    /**
     * Create shipping and billing address from Oink user data and associate it with the quote
     */
    protected function _createAddresses()
    {
        $user = $this->getUser();
        if ((bool)$user->getDeliverToChildren()) {
            $oinkAddress = $user->getSelectedChildrenAddress();
        } else {
            $oinkAddress = $user->getAddress();
        }
        $quote = $this->getQuote();
        $billingAddress = $quote->getBillingAddress();
        $billingAddress->addData($oinkAddress->getData());
        $billingAddress->setPaymentMethod(self::PAYMENT_METHOD_CODE);
        $billingAddressCopy = clone $billingAddress;
        $billingAddressCopy->unsAddressId()->unsAddressType();
        $shippingAddress = $this->getQuote()->getShippingAddress();

        $shippingMethodCode = $this->getUser()->getData('shipping_method');
        $shippingMethod = new Varien_Object(array("code" => $shippingMethodCode));

        $availableShippingMethods = $shippingAddress
            ->addData($billingAddressCopy->getData())
            ->setSameAsBilling(1)
            ->setSaveInAddressBook(0)
            ->setCollectShippingRates(true)
            ->collectShippingRates()
            ->save()
            ->getGroupedAllShippingRates();

        Mage::dispatchEvent("oink_after_set_shipping_method", array(
            "shipping_method" => $shippingMethod,
            "available_shipping_methods" => $availableShippingMethods,
        ));

        $shippingAddress
            ->setShippingMethod($shippingMethod->getCode())
            ->setCollectShippingRates(true);
    }

    /**
     * Get Oink cart from the quote
     * @return dtoCart
     */
    public function getOinkCart()
    {
        $quote = $this->getQuote();
        $totals = $quote->getTotals();
        $user = $this->getUser();
        if (!isset ($totals["shipping"])) {
            Mage::getSingleton('checkout/session')->setErrorMessage(Mage::getStoreConfig("oink/messages/shipping_error"));
        }
        $cart = Mage::helper("oink")->getDtoCart();
        if ((bool)$user->getDeliverToChildren()) {
            $cart->ShipmentAddress = $user->getSelectedChildrenAddressDto();
        } else {
            $cart->ShipmentAddress = $user->getAddressDto();
        }
        $this->_fillCartWithItems($cart, $quote);
        $cart->Currency = $quote->getBaseCurrencyCode();
        $cart->Total = $quote->getGrandTotal();
        $cart->ShippmentTotal = $quote->getGrandTotal();
        if (isset ($totals["tax"])) {
            $cart->Tax = $totals["tax"]->getValue();
        } else {
            $cart->Tax = 0;
        }
        if (isset($totals["shipping"])) {
            $cart->Cost = $totals["shipping"]->getValue();
        }
        $cart->Discount = $quote->getBaseSubtotal() - $quote->getBaseSubtotalWithDiscount();
        return $cart;
    }

    /**
     * Get Oink cart from the quote
     * @param dtoCart $cart
     * @return dtoResultObject
     */
    public function sendCartToOink($cart)
    {
        $user = $this->getUser();
        $order = $this->getOinkOrder();
        $paymentService = Mage::helper("oink")->getOinkPaymentService();
        if ($paymentService->nativeSoapExists()) {
            $data = $cart->toXml();
        } else {
            $data = $cart->toEscapedXml();
        }
        Mage::helper("oink")->log($data, "cartXML");
        $order->addAdditionalInformation("cartXML", $data);
        /*
         * $user-getToken() is saved in the database & tied to a transaction
         *
         */

        if ($user->getData("selected_children")) {
            $result = $paymentService->ProcessParentTransaction($user->getToken(), $data, "", $user->getData("selected_children"), $user->getSelectedPaymentAccount());
        } else {
            $result = $paymentService->ProcessTransaction($data, $user->getToken(), "");
        }

        $status = $paymentService->GetTransactionDetails($user->getToken(), $result->TransactionIdentifier);

        $order->addAdditionalInformation("processTransactionResponse", $result->Xml);
        return $result;
    }

    /*
     * 
     * @param string The transaction identifier
     */

    public function checkTransaction($transactionId)
    {
        $paymentService = Mage::helper("oink")->getOinkPaymentService();
        $user = $this->getUser();
        $details = $paymentService->GetTransactionDetails($user->getToken(), $transactionId);
        return $details;
    }

    /**
     *
     * @param int $orderId  The id of the magento order
     * @param string $field
     * @return Oink_Oink_Model_Order
     */

    public function getOinkOrder($orderId = null, $field = "order_id")
    {
        if (is_null($this->_oinkOrder)) {
            if (is_null($orderId)) {
                $this->_oinkOrder = Mage::getModel("oink/order");
            } else {
                $this->_oinkOrder = Mage::getModel("oink/order")->load($orderId, $field);
            }
        }
        return $this->_oinkOrder;
    }

    /*
     * 
     * @param Oink_Oink_Model_Order $order
     */

    public function completeOrder($oinkOrder)
    {
        /**
         * @var Mage_Sales_Model_Order_Invoice $invoice
         */
        $orderId = $oinkOrder->getOrderId();
        $order = Mage::getModel("sales/order")->load($orderId);

        try {
            if (!$order->canInvoice()) {
                Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
            }

            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();

            if ($this->isTwoStepsAuthorizationEnabled()) {
                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::NOT_CAPTURE);
                if($invoice->getOrder()->getState() != Mage_Sales_Model_Order::STATE_CANCELED) {
                    $order->getPayment()->setIsTransactionPending(true);
                }
            } else {
                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
            }

            $invoice->register();
            if($invoice->getOrder()->getState() != Mage_Sales_Model_Order::STATE_CANCELED) {
                $invoice->getOrder()->setIsInProcess(true);
            }

            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder());

            $transactionSave->save();
            $invoice->save();
        } catch (Mage_Core_Exception $e) {
            Mage::helper("oink")->log("OrderId: " . $orderId . " - " . $e->getMessage(), "errorCreatingInvoice");
        }

        if($order->getState() != Mage_Sales_Model_Order::STATE_CANCELED) {
            $order->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING);
            $order->save();
        }
    }

    /*
     * 
     * @param Oink_Oink_Model_Order $order
     */

    public function cancelOrder($oinkOrder)
    {
        $order = Mage::getModel("sales/order")->load($oinkOrder->getOrderId());
        $order->setStatus(Mage_Sales_Model_Order::STATE_CANCELED);
        $order->save();
    }

    /**
     *
     */
    public function cancelLastOrder()
    {
        $order = $this->getOrder();
        $order->setStatus(Mage_Sales_Model_Order::STATE_CANCELED);
        $order->save();
    }

    /**
     * Fill Oink cart with items from the quote
     * @param dtoCart $cart
     * @param Mage_Sales_Model_Quote $quote
     */
    protected function _fillCartWithItems($cart, $quote)
    {
        $items = $quote->getItemsCollection();
        foreach ($items as $key => $item) {
            if ((bool)$item->getParentItem()) {
                continue;
            }
            $vpItem = $this->_getCartItem($item);
            $cart->AddItem($vpItem);
        }
    }

    /**
     * Fill Oink cart with items from the quote
     * @param dtoCart $item
     * @param Mage_Sales_Model_Quote_Item
     */
    protected function _getCartItem($item)
    {
        $product = Mage::getModel("catalog/product")->load($item->getProductId());
        $itemDto = Mage::helper("oink")->getCartItemDto();
        $itemDto->Total = $item->getBaseRowTotal();
        $itemDto->Name = $item->getName();

        // If description is null set description to name
        $shortd = $product->getShortDescription();
        if ($shortd == "") {
            $itemDto->Description = $item->getName();
        } else {
            $itemDto->Description = $shortd;
        }

        $itemDto->Price = $item->getPrice();
        $itemDto->Quantity = $item->getQty();
        return $itemDto;
    }

    protected function _checkShippingMethod()
    {

    }

    /**
     * Get the checkout button as html
     * @return string
     */
    public function getCheckoutButtonHtml()
    {
        return Mage::app()->getLayout()->createBlock("oink/checkout_button")->toHtml();
    }

    /**
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @param Mage_Sales_Model_Order_Item $item
     * @return bool
     */
    protected function _isItemInInvoice($invoice, $item)
    {
        $collection = $invoice->getItemsCollection()->toArray();
        $collection = isset($collection['items']) ? $collection['items'] : array();

        /**
         * @var array $orderItem
         */
        foreach ($collection as $orderItem) {
            if ($orderItem['product_id'] == $item->getProductId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Fill the empty invoice with the current cart items
     *
     * @param $invoice Mage_Sales_Model_Order_Invoice
     * @param $order Mage_Sales_Model_Order
     */
    protected function _fillInvoiceWithItems($invoice, $order)
    {
        /**
         * @var Mage_Sales_Model_Convert_Order $convertor
         */
        $convertor = Mage::getModel('sales/convert_order');
        $savedQtys = array();

        /**
         * @var $orderItem Mage_Sales_Model_Order_Item
         */
        foreach ($order->getAllItems() as $orderItem) {

            if (!$orderItem->isDummy() && !$orderItem->getQtyToInvoice() && $orderItem->getLockedDoInvoice()) {
                continue;
            }

            if ($order->getForcedDoShipmentWithInvoice() && $orderItem->getLockedDoShip()) {
                continue;
            }

            $item = $convertor->itemToInvoiceItem($orderItem);

            if ($this->_isItemInInvoice($invoice, $item)) {
                continue;
            }

            if (isset($savedQtys[$orderItem->getId()])) {
                $qty = $savedQtys[$orderItem->getId()];
            } else {
                if ($orderItem->isDummy()) {
                    $qty = 1;
                } else {
                    $qty = $orderItem->getQtyToInvoice();
                }
            }
            $item->setQty($qty);
            $invoice->addItem($item);
        }
        $invoice->collectTotals();
    }
}
