<?php

class Oink_Oink_Block_Adminhtml_Sales_Order_View_Tab_Oink
    extends Mage_Adminhtml_Block_Sales_Order_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /*
     * @var $_order Oink_Oink_Model_Order
     */
    protected $_order;
    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('oink')->__('Oink');
    }

    public function getTabTitle()
    {
        return Mage::helper('oink')->__('Oink');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        $helper=Mage::helper("oink");
        $order=$this->getOrder();
        $vpOrder=Mage::helper("oink/checkout")->getOinkOrder($order->getId());
        $this->_order=$vpOrder;
        return !(bool)$vpOrder->getId();
    }
}
