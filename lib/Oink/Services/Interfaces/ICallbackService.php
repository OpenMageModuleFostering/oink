<?php
/**
 * @package Oink.Services.Interfaces
 */
    interface ICallbackService {
        /**
        * Method to authenticate parnet or child and returns a token to use in subsequent calls
        * @return Object containing information about call status, the user type and the token
        */		
        public function GetCallbackTransactionStatus();
        /**
        * Method to authenticate parnet or child and returns a token to use in subsequent calls
        * @return Object containing information about call status, the user type and the token
        */		
        public function GetCallbackAddressInformation();        
    }
?>