<?php

/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Model_Mysql4_Order_Collection
        extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('oink/order');
    }

}

?>
