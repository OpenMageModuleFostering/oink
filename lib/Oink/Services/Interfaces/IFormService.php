<?php
/**
 * @package Oink.Services.Interfaces
 */
    interface IFormService {
        /**
        * Method to authenticate parnet or child and returns a token to use in subsequent calls
        * @return Object containing information about call status, the user type and the token
        */		
        public function GenerateAddressRequestForm($config);
        /**
        * Method to authenticate parnet or child and returns a token to use in subsequent calls
        * @return Object containing information about call status, the user type and the token
        */		
        public function GenerateCheckoutForm($config);        
    }
?>