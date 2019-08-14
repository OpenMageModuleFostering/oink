<?php

/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Model_Order
        extends Mage_Core_Model_Abstract
{
    
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('oink/order');
    }
    
    
    
    /*
     * 
     * @param dtoResultObject
     * @param Mage_Sales_Model_Order
     */
    public function addAdditionalInformation($key,$value){
        $order=$this;
        $data=$order->getAdditionalInformation();
        $data[$key]=$value;
        $order->setAdditionalInformation($data);
    }




}

?>
