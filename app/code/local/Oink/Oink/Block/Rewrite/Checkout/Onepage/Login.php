<?php
/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Block_Rewrite_Checkout_Onepage_Login extends Mage_Checkout_Block_Onepage_Login
{
    
    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        Mage::getSingleton("customer/session")->unsParentConfirm();
        Mage::getSingleton("customer/session")->unsShippingMethodSelected();
        $button=Mage::helper("oink")->getCheckoutButtonHtml(array("print_form"=>true));
        return parent::_toHtml().$button;
    }
    
}
