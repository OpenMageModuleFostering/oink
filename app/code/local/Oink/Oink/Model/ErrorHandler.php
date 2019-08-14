<?php

/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Model_ErrorHandler
    extends Mage_Core_Model_Abstract
{

    protected $_errors = array(
        "Merchant Application is not approved for your profile. Please add the merchant application to your profile." => array("type" => "config", "path" => "oink/messages/transaction_not_authorized"),
        "The Transaction was denied because you do not have enough funds available."                                  => array("type" => "config", "path" => "oink/messages/insufficient_funds"),
        "Payment Account is disabled. Please contact your parent."                                                    => array("type" => "config", "path" => "oink/messages/parent_configuration"),
        "parent_configuration"                                                                                        => array("type" => "config", "path" => "oink/messages/parent_configuration"),
        "The payment type associated with your account is not accepted by this merchant."                             => array("type" => "config", "path" => "oink/messages/payment_not_accepted")
    );

    public function rewriteError($originalErrorMessage)
    {
        if($originalErrorMessage == ''){
            return Mage::getStoreConfig("oink/messages/transaction_declined");
        }
        foreach ($this->_errors as $errorMessage => $errorConfig) {
            if(strpos($originalErrorMessage, $errorMessage)!==false){
                if($errorConfig["type"]=="config"){
                    return Mage::getStoreConfig($errorConfig["path"]);
                }
            }
        }

        return $originalErrorMessage;
    }

}
