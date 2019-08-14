<?php
/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Block_Checkout_ParentConfirm extends Mage_Core_Block_Template
{
    /**
     *
     * @return  string
     */
    public function getFormActionUrl(){
        return $this->getUrl("oink/checkout/processParentConfirm");
    }

}
