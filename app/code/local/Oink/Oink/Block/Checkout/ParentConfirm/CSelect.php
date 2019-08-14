<?php
/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Block_Checkout_ParentConfirm_CSelect
        extends Mage_Core_Block_Template
{
    public function getChildrens(){
        $helper=Mage::helper("oink");
        $user=$helper->getUser();
        $childrens=$user->getChildrens();
        return $childrens;
    }
}