<?php
/**
 * @package Oink.Services.Interfaces
 */
    interface IPaymentService {

        public function AddItemToMerchantExclusionList();
        public function AuthenticateChild($name, $password);
        public function GetChildProfile($token);
        public function GetChildAddress($token);
        public function GetLoyaltyBalance($token);
        public function GetTransactionDetails($token, $transactionIdentifier);
        public function GetTransactions($token, $start_date, $end_date);
        public function ProcessParentTransaction($token, $checkOutData,  $transactionDescription, $childIdentifier, $paymentAccountIdentifier);
        public function ProcessTransaction($checkOutData, $token, $transactionDescription);
        public function RemoveItemFromMerchantExclusionList();
        public function SaveWishList($token, $xml);
        /*
        * TODO: Need to implement PingHeaders prior to Guest checkout
        * Will work on this when other tasks in 1st phase are complete.
        * */
        /**
        * Used to verify connectivity to the web service
        * and verifies validity for the header values vp.MerchantIdentifier and vp.APIkey
        */		
        public function  PingHeaders();
        /*
        * ============================================================================
        * TODO: Will be implemented after 1st phase of project is considered complete.
        * ============================================================================
        * */		
        /**
        * Method to authenticate parnet or child and returns a token to use in subsequent calls
        * @return Object containing information about call status, the user type and the token
        */		
         public function AuthenticateUser($name, $password);
        /**
        * Method to return parent address details
        * <param name="token">Parent Security Token</param>
        * @return Transaction Result object
        */		
        //public function GetParentAddress($token);
        /**
        * Method to return a Parent's address details
        * <param name="token">Parent Security Token</param>
        * @return Address Result object
        */		
        public function  GetParentAddress($token);
         //public function GetParentChildAddress($token, $childIdentifier);
        /**
        * Method to return a Parent child's address details
        * <param name="token">Parent Security Token</param>
        * @return Address Result object
        */		
        public function  GetParentChildAddress($token,$childIdentifier);
        /**
        * Method to return a Parent's payment accounts compatible with merchant application
        * <param name="token">Parent Security Token</param>
        * @return Array of entities
         */		
        public function GetPaymentAccounts($token);
        /**
        * Method to return a Parent's list of children he can purchase items for
        * <param name="token">Parent Security Token</param>
        * @return Array of entities 
        */		
        public function GetAllChildren($token);
    }
?>