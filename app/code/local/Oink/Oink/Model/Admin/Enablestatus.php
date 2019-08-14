<?php

class Oink_Oink_Model_Admin_Enablestatus {
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('oink')->__('None')),
            array('value'=>1, 'label'=>Mage::helper('oink')->__('All Users')),
            array('value'=>2, 'label'=>Mage::helper('oink')->__('Only Registered Users')),
            array('value'=>3, 'label'=>Mage::helper('oink')->__('Only Guest Users')),
        );
    }
}