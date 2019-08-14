<?php
/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Block_Checkout_Review_Info extends Mage_Sales_Block_Items_Abstract
{
    
    public function __construct()
    {
        parent::__construct();
        $this->_canEditQty=false;
    }

    /**
     * Get array of all items what can be display directly
     *
     * @return array
     */
    public function getItems()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
    }
    /**
     * Get all quote totals (sorted by priority)
     * Method process quote states isVirtual and isMultiShipping
     *
     * @return array
     */
    public function getTotals()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getTotals();
    }
}
