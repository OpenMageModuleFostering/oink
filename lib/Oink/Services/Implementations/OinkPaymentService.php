<?php

/**
 * @package Oink.Services.Implementations
 */
class OinkPaymentService implements IPaymentService {

    var $config;

    function __construct($config) {
        $this->config = $config;
    }
    private function GetVPSoapClient(){
        $client = new OinkSoapClient($this->config);
        return $client;
    }
    /**
     * Default method to generate soap client with WCF security headers
     * set properly
     */
    private function GetNativeSoapClient() {
        $client = new SOAPClient($this->config->TransactionServiceEndpointAddressWsdl, array("trace" => 1));
        $headers = array();

        $headers[] = new SoapHeader($this->config->HeaderNamespace,
                        $this->config->propMerchantIdentifier,
                        $this->config->MerchantIdentifier);

        $headers[] = new SoapHeader($this->config->HeaderNamespace,
                        $this->config->propApiKey,
                        $this->config->APIkey);
        $client->__setSoapHeaders($headers);
        
        return $client;
    }
    
    private function GetSoapClient(){
        if($this->nativeSoapExists()){
            return $this->GetNativeSoapClient();
        }else{
            return $this->GetVPSoapClient();
        }
    }

    public function nativeSoapExists(){
        return class_exists('SOAPClient');
    }
    
    public function AddItemToMerchantExclusionList() {
        
    }

    /**
     * Authenticates a child based on username & password.
     * @return dtoResultObject The result object contains information for
     * the webservice call to AuthenticateChild
     */
    public function AuthenticateChild($name, $password) {
        $result_dto = new dtoResultObject();
        $result_dto->ErrorMessage = "SOAP call not executed.";
        $result_dto->Status = false;

        try {
            $client = $this->GetSoapClient();
            $params = array(
                'userName' => $name,
                'password' => $password,
            );
            $result = $client->AuthenticateChild($params);
            $result_dto->ErrorMessage = $result->AuthenticateChildResult->ErrorMessage;
            $result_dto->Token = $result->AuthenticateChildResult->Token;
            $result_dto->Status = $result->AuthenticateChildResult->Status;

        } catch (Exception $e) {
            $result_dto->ErrorMessage = "Exception occured: " . $e;
        }
        return $result_dto;
    }

    /**
     * Method to authenticate parnet or child and returns a token to use in subsequent calls
     * @return Object containing information about call status, the user type and the token
     */
    public function AuthenticateUser($name, $password) {
        $result_dto = new dtoResultObject();
        $result_dto->ErrorMessage = "SOAP call not executed.";
        $result_dto->Status = false;

        try {
            $client = $this->GetSoapClient();
            $params = array(
                'userName' => $name,
                'password' => $password,
            );
            $result = $client->AuthenticateUser($params);
            $result_dto->ErrorMessage = $result->AuthenticateUserResult->ErrorMessage;
            $result_dto->Token = $result->AuthenticateUserResult->Token;
            $result_dto->Status = $result->AuthenticateUserResult->Status;
            $result_dto->UserType = $result->AuthenticateUserResult->UserType;
        } catch (Exception $e) {
            $result_dto->ErrorMessage = "Exception occured: " . $e;
        }
        return $result_dto;
    }

    public function GetChildProfile($token) {
        $profile = new dtoProfileInfo();
        $profile->ErrorMessage = "SOAP call not executed.";
        try {
            $client = $this->GetSoapClient();
            $params = array(
                'token' => $token,
            );
            $result = $client->GetChildGenderAge($params);

            $profile->Age = $result->GetChildGenderAgeResult->Age;
            $profile->Gender = $result->GetChildGenderAgeResult->Gender;
            $profile->ErrorMessage = $result->GetChildGenderAgeResult->ErrorMessage;
        } catch (Exception $e) {
            $profile->ErrorMessage = "Exception occured: " . $e;
        }
        return $profile;
    }

    /**
     * Gets child address data based on token obtained from AuthenticateChild method
     * @return dtoAddress The address object contains child data returned from
     * the webservice call to GetChildAddress
     */
    public function GetChildAddress($token) {
        $result_dto = new dtoAddress();
        $result_dto->Status = false;
        $result_dto->ErrorMessage = "SOAP call not executed.";
        try {
            $client = $this->GetSoapClient();
            $params = array(
                'token' => $token,
            );
            $result = $client->GetChildAddress($params);
            $result_dto->Address = $result->GetChildAddressResult->Address;
            $result_dto->City = $result->GetChildAddressResult->City;
            $result_dto->Country = $result->GetChildAddressResult->Country;
            $result_dto->ErrorMessage = $result->GetChildAddressResult->ErrorMessage;
            $result_dto->State = $result->GetChildAddressResult->State;
            $result_dto->Status = $result->GetChildAddressResult->Status;
            $result_dto->Zip = $result->GetChildAddressResult->Zip;
            $result_dto->Phone = $result->GetChildAddressResult->ParentPhone;
            $result_dto->ParentEmail = $result->GetChildAddressResult->ParentEmail;
            $result_dto->ParentName = $result->GetChildAddressResult->Name;
            $result_dto->ChildName = $result->GetChildAddressResult->AttentionOf;
        } catch (Exception $e) {
            $result_dto->ErrorMessage = "Exception occured: " . $e;
        }
        return $result_dto;
    }

    /**
     * Gets parent address data based on token obtained from AuthenticateUser method
     * @return dtoAddress The address object contains child data returned from
     * the webservice call to GetParentAddress
     */
    public function GetParentAddress($token) {
        $result_dto = new dtoAddress();
        $result_dto->Status = false;
        $result_dto->ErrorMessage = "SOAP call not executed.";
        try {
            $client = $this->GetSoapClient();
            $params = array(
                'token' => $token,
            );
            $result = $client->GetParentAddress($params);
            $result_dto->Address = $result->GetParentAddressResult->Address;
            $result_dto->City = $result->GetParentAddressResult->City;
            $result_dto->Country = $result->GetParentAddressResult->Country;
            $result_dto->ErrorMessage = $result->GetParentAddressResult->ErrorMessage;
            $result_dto->State = $result->GetParentAddressResult->State;
            $result_dto->Status = $result->GetParentAddressResult->Status;
            $result_dto->Zip = $result->GetParentAddressResult->Zip;
            $result_dto->Phone = $result->GetParentAddressResult->ParentPhone;
            $result_dto->ParentEmail = $result->GetParentAddressResult->ParentEmail;
            $result_dto->ParentName = $result->GetParentAddressResult->Name;
        } catch (Exception $e) {
            $result_dto->ErrorMessage = "Exception occured: " . $e;
        }
        return $result_dto;
    }

    /**
     * Method to return a Parent child's address details
     * <param name="token">Parent Security Token</param>
     * @return Address Result object
     */
    public function GetParentChildAddress($token, $childIdentifier) {
        $result_dto = new dtoAddress();
        $result_dto->Status = false;
        $result_dto->ErrorMessage = "SOAP call not executed.";
        try {
            $client = $this->GetSoapClient();
            $params = array(
                'token' => $token,
                "childIdentifier" => $childIdentifier
            );
            $result = $client->GetParentChildAddress($params);
            $result_dto->Address = $result->GetParentChildAddressResult->Address;
            $result_dto->City = $result->GetParentChildAddressResult->City;
            $result_dto->Country = $result->GetParentChildAddressResult->Country;
            $result_dto->ErrorMessage = $result->GetParentChildAddressResult->ErrorMessage;
            $result_dto->State = $result->GetParentChildAddressResult->State;
            $result_dto->Status = $result->GetParentChildAddressResult->Status;
            $result_dto->Zip = $result->GetParentChildAddressResult->Zip;
            $result_dto->Phone = $result->GetParentChildAddressResult->ParentPhone;
            $result_dto->ParentEmail = $result->GetParentChildAddressResult->ParentEmail;
            $result_dto->ParentName = $result->GetParentChildAddressResult->Name;
        } catch (Exception $e) {
            $result_dto->ErrorMessage = "Exception occured: " . $e;
        }
        return $result_dto;
    }

    /**
     * Method to return a Parent's list of children he can purchase items for
     * <param name="token">Parent Security Token</param>
     * @return Array of entities 
     */
    public function GetAllChildren($token) {
        $result_dto = new dtoResultObject();
        $result_dto->ErrorMessage = "SOAP call not executed.";
        $result_dto->Status = false;

        try {
            $client = $this->GetSoapClient();
            $params = array(
                'token' => $token,
            );
            $result = $client->GetAllChildren($params);
            $childrens = array();
            if (!is_array($result->GetAllChildrenResult->EntityResult)) {
                $result->GetAllChildrenResult->EntityResult = array($result->GetAllChildrenResult->EntityResult);
            }
            foreach ($result->GetAllChildrenResult->EntityResult as $key => $_children) {
                $_children=(object)$_children;
                $children = new dtoChildren();
                $children->Name = $_children->Name;
                $children->Token = $_children->Identifier;
                $childrens[] = $children;
            }
        } catch (Exception $e) {
            $result_dto->ErrorMessage = "Exception occured: " . $e;
            return $result_dto;
        }
        return $childrens;
    }

    /**
     * Method to return a Parent's list of children he can purchase items for
     * <param name="token">Parent Security Token</param>
     * @return array
     */
    public function GetPaymentAccounts($token) {
        $result_dto = new dtoResultObject();
        $result_dto->ErrorMessage = "SOAP call not executed.";
        $result_dto->Status = false;

        try {
            $client = $this->GetSoapClient();
            $params = array(
                'token' => $token,
            );
            $result = $client->GetPaymentAccounts($params);
            if (!is_array($result->GetPaymentAccountsResult->PaymentAccountResult)) {
                $result->GetPaymentAccountsResult->PaymentAccountResult = array($result->GetPaymentAccountsResult->PaymentAccountResult);
            }
            $paymentAccounts = array();
            foreach ($result->GetPaymentAccountsResult->PaymentAccountResult as $key => $_paymentAccount) {
                $_paymentAccount = (object) $_paymentAccount;
                $paymentAccount = new dtoPaymentAccount();
                $paymentAccount->Name = $_paymentAccount->Name;
                $paymentAccount->Type = $_paymentAccount->Type;
                $paymentAccount->Token = $_paymentAccount->Identifier;
                $paymentAccount->Url = $_paymentAccount->Url;
                $paymentAccounts[] = $paymentAccount;
            }
        } catch (Exception $e) {
            $result_dto->ErrorMessage = "Exception occured: " . $e;
            return $result_dto;
        }
        return $paymentAccounts;
    }

    public function GetLoyaltyBalance($token) {
        
    }

    /**
     * Gets specific transaction data based on token obtained from AuthenticateChild method
     * & transaction identifier obtained from ProcessTransaction method
     */
    public function GetTransactionDetails($token, $transactionIdentifier) {

        try {
            $client = $this->GetSoapClient();
            $params = array(
                'token' => $token,
                'transactionIdentifier' => $transactionIdentifier,
            );
            /*
             * TODO: Need to create dtoTransactionDetails
             * Need to map to dto so integration developers will understand
             * what data is comming out of service. 
             */
            $return = $client->GetTransactionDetails($params);
        } catch (Exception $e) {
            /*
             * TODO: Need to add exception message to dtoTransactionDetails DTO
             */
            return $e;
        }
        return $return;
    }

    public function GetTransactions($token, $start_date, $end_date) {

        try {
            $client = $this->GetSoapClient();

            $params = array(
                'token' => $token,
                'startDate' => $start_date,
                'endDate' => $end_date,
            );
            $return = $client->GetTransactions($params);
        } catch (Exception $e) {
            echo "Exception occured: " . $e;
        }
    }

    public function ProcessTransaction($checkOutData, $token, $transactionDescription) {
        $result_dto = new dtoResultObject();
        $result_dto->Status = false;
        $result_dto->ErrorMessage = "SOAP call not executed.";
        try {
            $client = $this->GetSoapClient();
            $params = array(
                'checkOutData' => $checkOutData,
                'token' => $token,
                'transactionDescription' => $transactionDescription,
            );
            $result = $client->ProcessTransaction($params);
            if ((string)$result->ProcessTransactionResult->Status == 'false'){
                $result->ProcessTransactionResult->Status = false;
            }    
            $result_dto->Xml = $client->__getLastResponse();
            $result_dto->ErrorMessage = $result->ProcessTransactionResult->ErrorMessage;
            $result_dto->Status = $result->ProcessTransactionResult->Status;
            $result_dto->TransactionStatus = $result->ProcessTransactionResult->TransactionStatus;
            $result_dto->TransactionIdentifier = $result->ProcessTransactionResult->TransactionIdentifier;
        } catch (Exception $e) {
            $result_dto->ErrorMessage = "Exception occured: " . $e;
        }
        return $result_dto;
    }

    public function ProcessParentTransaction($token, $checkOutData, $transactionDescription, $childIdentifier, $paymentAccountIdentifier) {
        $result_dto = new dtoResultObject();
        $result_dto->Status = false;
        $result_dto->ErrorMessage = "SOAP call not executed.";
        try {
            $client = $this->GetSoapClient();
            $params = array(
                'token' => $token,
                'checkOutData' => $checkOutData,
                'transactionDescription' => $transactionDescription,
                'childIdentifier' => $childIdentifier,
                'paymentAccountIdentifier' => $paymentAccountIdentifier,
            );
            $result = $client->ProcessParentTransaction($params);
            if ((string)$result->ProcessParentTransactionResult->Status == 'false'){
                $result->ProcessParentTransactionResult->Status = false;
            }    
            $result_dto->Xml = $client->__getLastResponse();
            $result_dto->ErrorMessage = $result->ProcessParentTransactionResult->ErrorMessage;
            $result_dto->Status = $result->ProcessParentTransactionResult->Status;
            $result_dto->TransactionStatus = $result->ProcessParentTransactionResult->TransactionStatus;
            $result_dto->TransactionIdentifier = $result->ProcessParentTransactionResult->TransactionIdentifier;
        } catch (Exception $e) {
            $result_dto->ErrorMessage = "Exception occured: " . $e;
        }
        return $result_dto;
    }

    public function ProcessSubscription($token, $subscriptionData, $checkOutData, $description) {
        $result_dto = new dtoResultObject();
        $result_dto->Status = false;
        $result_dto->ErrorMessage = "SOAP call not executed.";
        try {
            $client = $this->GetSoapClient();
            $params = array(
                'token' => $token,
                'subscriptionData' => $subscriptionData,
                'checkOutData' => $checkOutData,
                'transactionDescription' => $description,
            );
            $result = $client->ProcessSubscription($params);
            $result_dto->Xml = $client->__getLastResponse();
            $result_dto->Identifier = $result->ProcessSubscriptionResult->Identifier;
            $result_dto->Name = $result->ProcessSubscriptionResult->Name;
            $result_dto->Type = $result->ProcessSubscriptionResult->Type;
        } catch (Exception $e) {
            $result_dto->ErrorMessage = "Exception occured: " . $e;    
        }
        return $result;
    }
    
    public function ApproveSubscription($token, $subscriptionIdentifier, $paymentAccountIdentifier, $endDate, $maxCount, $approved) {
        $result_dto = new dtoResultObject();
        $result_dto->Status = false;
        $result_dto->ErrorMessage = "SOAP call not executed.";
        try {
            $client = $this->GetSoapClient();
            $params = array(
                'token' => $token,
                'subscriptionIdentifier' => $subscriptionIdentifier,
                'paymentAccountIdentifier' => $paymentAccountIdentifier,
                'endDate' => $endDate,
                'maxCount' => $maxCount,
                'approved' => $approved,
            );
            $result = $client->ApproveSubscription($params);
        } catch (Exception $e) {
            $result_dto->ErrorMessage = "Exception occured: " . $e;
            return $result_dto;    
        }
        return $result;
    }
    
    public function MerchantCancelSubscription($Identifier){
        $result_dto = new dtoResultObject();
        $result_dto->Status = false;
        $result_dto->ErrorMessage = "SOAP call not executed.";
        try {
            $client = $this->GetSoapClient();
            $params = array(
                'Identifier' => $Identifier,
            );
            $result = $client->ApproveSubscription($params);
        } catch (Exception $e) {
            $result_dto->ErrorMessage = "Exception occured: " . $e;
            return $result_dto;    
        }
        return $result;
    }
    
    public function MerchantCancelSubscriptionByExternalRef($ExternalRefIdentifier){
        $result_dto = new dtoResultObject();
        $result_dto->Status = false;
        $result_dto->ErrorMessage = "SOAP call not executed.";
        try {
            $client = $this->GetSoapClient();
            $params = array(
                'ExternalRefIdentifier' => $ExternalRefIdentifier,
            );
            $result = $client->ApproveSubscription($params);
        } catch (Exception $e) {
            $result_dto->ErrorMessage = "Exception occured: " . $e;
            return $result_dto;    
        }
        return $result;
    }

    public function GetSubscriptionTransactions($subscriptionIdentifier){
        $result_dto = new dtoResultObject();
        $result_dto->Status = false;
        $result_dto->ErrorMessage = "SOAP call not executed.";
        try {
            $client = $this->GetSoapClient();
            $params = array(
                'subscriptionIdentifier' => $subscriptionIdentifier,
            );
            $result = $client->ApproveSubscription($params);
        } catch (Exception $e) {
            $result_dto->ErrorMessage = "Exception occured: " . $e;
            return $result_dto;    
        }
        return $result;
    }
    
    public function GetSubscriptionTransactionsByRef($externalIdentifier){
        $result_dto = new dtoResultObject();
        $result_dto->Status = false;
        $result_dto->ErrorMessage = "SOAP call not executed.";
        try {
            $client = $this->GetSoapClient();
            $params = array(
                'ExternalRefIdentifier' => $externalIdentifier,
            );
            $result = $client->ApproveSubscription($params);
        } catch (Exception $e) {
            $result_dto->ErrorMessage = "Exception occured: " . $e;
            return $result_dto;    
        }
        return $result;
    }

    public function RemoveItemFromMerchantExclusionList() {
        
    }

    public function SaveWishList($token, $xml) {

        try {
            $client = $this->GetSoapClient();
            $params = array(
                'token' => $token,
                'wishListXml' => $xml,
            );
            $result = $client->SaveWishList($params);
        } catch (Exception $e) {
            echo "Exception occured: " . $e;
        }
        $status = new dtoWishlistStatus();
        $status->Children = $result->SaveWishListResult->Children;
        $status->ErrorMessage = $result->SaveWishListResult->ErrorMessage;
        $status->Status = $result->SaveWishListResult->Status;
        $status->Token = $result->SaveWishListResult->Token;
        $status->TransactionStatus = $result->SaveWishListResult->TransactionStatus;
        return $status;
    }

    public function PingHeaders() {
        try {
            $client = $this->GetSoapClient();
            $result = $client->PingHeaders();
        } catch (Exception $e) {
            return false;
        }
        if(isset($result->PingHeadersResult->scalar)){
           return ($result->PingHeadersResult->scalar == 'true')?1:0;
        }
        return $result->PingHeadersResult;
    }

    public function CaptureTransactionByIdentifier($transactionIdentifier){
        $result_dto = new dtoResultObject();
        $result_dto->Status = false;
        $result_dto->ErrorMessage = "SOAP call not executed.";
        try {
            $client = $this->GetSoapClient();
            $params = array(
                'transactionIdentifier' => $transactionIdentifier,
                'capture' => true,
            );

            $result = $client->CaptureTransactionByIdentifier($params);
            $result_dto->ErrorMessage = $result->CaptureTransactionByIdentifierResult->ErrorMessage;
            $result_dto->Status = $result->CaptureTransactionByIdentifierResult->Status;
        } catch (Exception $e) {

            $result_dto->ErrorMessage = "Exception occured: " . $e;
        }
        return $result_dto;
    }

    public function VoidCaptureTransactionByIdentifier($transactionIdentifier){
        $result_dto = new dtoResultObject();
        $result_dto->Status = false;
        $result_dto->ErrorMessage = "SOAP call not executed.";
        try {
            $client = $this->GetSoapClient();
            $params = array(
                'transactionIdentifier' => $transactionIdentifier,
                'capture' => 0,
            );

            $result = $client->CaptureTransactionByIdentifier($params);
            $result_dto->ErrorMessage = $result->CaptureTransactionByIdentifierResult->ErrorMessage;
            $result_dto->Status = $result->CaptureTransactionByIdentifierResult->Status;
        } catch (Exception $e) {
            $result_dto->ErrorMessage = "Exception occured: " . $e;
        }
        return $result_dto;
    }

}

?>
