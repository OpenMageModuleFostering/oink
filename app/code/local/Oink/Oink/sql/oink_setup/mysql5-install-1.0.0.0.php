<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('oink_order')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('oink_order')}` (
  `oink_order_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `transaction_identifier` varchar(128) NOT NULL,
  `expiry_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `additional_information` text NOT NULL,
  PRIMARY KEY (`oink_order_id`),
  UNIQUE KEY `oink_order_sales_flat_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `{$this->getTable('oink_order')}`
  ADD CONSTRAINT `oink_order_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `{$this->getTable('sales_flat_order')}` (`entity_id`) ON DELETE CASCADE ON UPDATE NO ACTION;
");

$installer->endSetup();
