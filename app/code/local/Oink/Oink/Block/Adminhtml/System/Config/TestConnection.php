<?php

class Oink_Oink_Block_Adminhtml_System_Config_TestConnection
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $block=Mage::app()->getLayout()->createBlock("oink/adminhtml_system_config_testConnection_html");
        return $block->_toHtml();
    }
}