<?php
/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Block_Payment_Info_Oink
        extends Mage_Payment_Block_Info
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('oink/payment/info/oink.phtml');
    }
    /**
     * Render as PDF
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('oink/payment/info/oink.phtml');
        return $this->toHtml();
    }

}
