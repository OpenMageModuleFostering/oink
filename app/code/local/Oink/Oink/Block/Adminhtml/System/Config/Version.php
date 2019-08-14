<?php

class Oink_Oink_Block_Adminhtml_System_Config_Version
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $config=Mage::getConfig();
        $version=(string)$config->getNode("modules/Oink_Oink/version");
        return $version;
    }
}