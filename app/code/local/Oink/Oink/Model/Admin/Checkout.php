<?php

class Oink_Oink_Model_Admin_Checkout {
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('oink')->__('Checkout Button and Radio Select')),
            array('value'=>1, 'label'=>Mage::helper('oink')->__('Checkout Button Only')),
            array('value'=>2, 'label'=>Mage::helper('oink')->__('Radio Select Only')),
        );
    }
}