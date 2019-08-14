<?php
/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Block_Payment_Form_Oink
        extends Mage_Payment_Block_Form
{

    protected function _construct()
    {
        $this->setMethodLabelAfterHtml(Mage::helper("oink")->getCheckoutButtonHtml());
        parent::_construct();
        $this->setTemplate('oink/payment/form/oink.phtml');
    }

}
