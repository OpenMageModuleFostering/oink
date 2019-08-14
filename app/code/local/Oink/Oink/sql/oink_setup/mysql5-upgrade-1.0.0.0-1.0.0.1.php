<?php
$installer = $this;

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();

try {
    $installer->getConnection()->addColumn(
        $installer->getTable('sales/order'),
        'oink_status',
        'int(4)'
    );

    $installer->endSetup();
} catch (Exception $e){
    $this->getConnection()->rollback();
    Mage::logException($e);
}


