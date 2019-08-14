<?php
class OinkSoapClient{
    var $config;
    var $lastResponse;
    
    function __construct($config) {
        $this->config = $config;
    }
    
    public function AuthenticateChild($params){
        $method = "AuthenticateChild";
        $response = $this->MakeGenericSoapCall($method,$params);        
        $result = new stdClass();
        $result->AuthenticateChildResult = new stdClass();    
        $result->AuthenticateChildResult = $response;
        return $result;
    }
    
    public function AuthenticateUser($params){
        $method = "AuthenticateUser";
        $response = $this->MakeGenericSoapCall($method,$params);
        $result = new stdClass();
        $result->AuthenticateUserResult = new stdClass();
        $result->AuthenticateUserResult = $response;
        return $result;
    }
    
    public function GetChildGenderAge($params){
        $method = "GetChildGenderAge";
        $response = $this->MakeGenericSoapCall($method,$params);
        $result = new stdClass();
        $result->GetChildGenderAgeResult = new stdClass();
        $result->GetChildGenderAgeResult = $response;
        return $result;
    }
    
    public function GetChildAddress($params){
        $method = "GetChildAddress";
        $response = $this->MakeGenericSoapCall($method,$params);        

        $result = new stdClass();
        $result->GetChildAddressResult = new stdClass();
        $result->GetChildAddressResult = $response;
        return $result;
    }
    
    public function GetParentAddress($params){
        $method = "GetParentAddress";
        $response = $this->MakeGenericSoapCall($method,$params);        

        $result = new stdClass();
        $result->GetParentAddressResult = new stdClass();     
        $result->GetParentAddressResult = $response;
        return $result;
    }
    
    public function GetParentChildAddress($params){
        $method = "GetParentChildAddress";
        $response = $this->MakeGenericSoapCall($method,$params);     
        $result = new stdClass();
        $result->GetParentChildAddressResult = new stdClass();           
        $result->GetParentChildAddressResult = $response;
        return $result;
    }
    
    public function GetAllChildren($params){
        $method = "GetAllChildren";
        $response = $this->MakeGenericSoapCall($method,$params);        
        $result = new stdClass();
        $result->GetAllChildrenResult = new stdClass();           
        $result->GetAllChildrenResult = $response;
        return $result;
    }
    
    public function GetPaymentAccounts($params){
        $method = "GetPaymentAccounts";
        $response = $this->MakeGenericSoapCall($method,$params);        
        $result = new stdClass();
        $result->GetPaymentAccountsResult = new stdClass();           
        $result->GetPaymentAccountsResult = $response;
        return $result;
    }
    
    public function GetLoyaltyBalance($params){
        $method = "GetLoyaltyBalance";
        $response = $this->MakeGenericSoapCall($method,$params);        
        $result = new stdClass();
        $result->GetLoyaltyBalanceResult = new stdClass();           
        $result->GetLoyaltyBalanceResult = $response;
        return $result;    
    }
    
    public function GetTransactionDetails($params){
        $method = "GetTransactionDetails";
        $response = $this->MakeGenericSoapCall($method,$params);        
        $result = new stdClass();
        $result->GetTransactionDetailsResult = new stdClass();           
        $result->GetTransactionDetailsResult = $response;
        return $result;
    }

    public function GetTransactions($params){
        $method = "GetTransactions";
        $response = $this->MakeGenericSoapCall($method,$params);        
        $result = new stdClass();
        $result->GetTransactionsResult = new stdClass();           
        $result->GetTransactionsResult = $response;
        return $result;
    }
    
    public function ProcessTransaction($params){
        $method = "ProcessTransaction";
        $response = $this->MakeGenericSoapCall($method,$params);        
        $result = new stdClass();
        $result->ProcessTransactionResult = new stdClass();           
        $result->ProcessTransactionResult = $response;
        return $result;
    }
    
    public function ProcessParentTransaction($params){
        $method = "ProcessParentTransaction";
        $response = $this->MakeGenericSoapCall($method,$params);        
        $result = new stdClass();
        $result->ProcessParentTransactionResult = new stdClass();           
        $result->ProcessParentTransactionResult = $response;
        return $result;
    }
    
    public function ProcessSubscription($params){
        $method = "ProcessSubscription";
        $response = $this->MakeGenericSoapCall($method,$params);        
        $result = new stdClass();
        $result->ProcessSubscriptionResult = new stdClass();           
        $result->ProcessSubscriptionResult = $response;
        return $result;
    }
    
    public function ApproveSubscription($params) {
        $method = "ApproveSubscription";
        $response = $this->MakeGenericSoapCall($method,$params);        
        $result = new stdClass();
        $result->ApproveSubscriptionResult = new stdClass();           
        $result->ApproveSubscriptionResult = $response;
        return $result;
    }
    
    public function MerchantCancelSubscription($params){
        $method = "MerchantCancelSubscription";
        $response = $this->MakeGenericSoapCall($method,$params);        
        $result = new stdClass();
        $result->MerchantCancelSubscriptionResult = new stdClass();           
        $result->MerchantCancelSubscriptionResult = $response;
        return $result;
    }
    
    public function MerchantCancelSubscriptionByExternalRef($params){
        $method = "MerchantCancelSubscriptionByExternalRef";
        $response = $this->MakeGenericSoapCall($method,$params);        
        $result = new stdClass();
        $result->MerchantCancelSubscriptionByExternalRefResult = new stdClass();           
        $result->MerchantCancelSubscriptionByExternalRefResult = $response;
        return $result;
    }

    public function GetSubscriptionTransactions($params){
        $method = "GetSubscriptionTransactions";
        $response = $this->MakeGenericSoapCall($method,$params);        
        $result = new stdClass();
        $result->GetSubscriptionTransactionsResult = new stdClass();           
        $result->GetSubscriptionTransactionsResult = $response;
        return $result;
    }
    
    public function GetSubscriptionTransactionsByRef($params){
        $method = "GetSubscriptionTransactionsByRef";
        $response = $this->MakeGenericSoapCall($method,$params);        
        $result = new stdClass();
        $result->GetSubscriptionTransactionsByRefResult = new stdClass();           
        $result->GetSubscriptionTransactionsByRefResult = $response;
        return $result;
    }

    public function SaveWishList($params){
        $method = "SaveWishList";
        $response = $this->MakeGenericSoapCall($method,$params);        
        $result = new stdClass();
        $result->SaveWishListResult = new stdClass();           
        $result->SaveWishListResult = $response;
        return $result;
    }

    public function CaptureTransactionByIdentifier($params){
        $method = "CaptureTransactionByIdentifier";
        $response = $this->MakeGenericSoapCall($method,$params);
        $result = new stdClass();
        $result->CaptureTransactionByIdentifierResult = new stdClass();
        $result->CaptureTransactionByIdentifierResult = $response;
        return $result;
    }
    
    public function PingHeaders(){
        $method = "PingHeaders";
        $response = $this->MakeGenericSoapCall($method,NULL);     
        $result = new stdClass();
        $result->PingHeadersResult = new stdClass();           
        $result->PingHeadersResult = $response;
        return $result;
    }
    
    public function __getLastResponse(){
        return $this->lastResponse;
    }
    
    /*==============================================
    * Soap Infrastructure
    ==============================================*/
    private function MakeGenericSoapCall($method, $params){ 
        $soap_envelope = $this->GenerateSoapEnvelope($method, $params);
        $url = $this->config->TransactionServiceEndpointAddress."?".$method;
        $SOAPAction = 'http://tempuri.org/ITransactionService/'.$method;

        $headers = array(
            'Content-Type: text/xml; charset=utf-8',
            'Content-Length: '.strlen($soap_envelope),
            'SOAPAction: '.$SOAPAction
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $soap_envelope);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $result = curl_exec($ch);
        $this->lastResponse = $result;
        curl_close($ch);
        
        $response = $this->ConvertSoapXmlToResponseObject($method, $result);
        $this->CheckResponse($response);
        return $response;
    }    
    private function GenerateSoapEnvelope($method, $params){
        
        $body = "<ns1:".$method.">";
        if (isset ($params)) {
            foreach ($params as $key => $value) {
                $p = '<ns1:' . $key . '>' . $value . '</ns1:' . $key . '>';
                $body .= $p;
            }
        }
        $body .="</ns1:".$method.">";
        
        $envelope = '
                <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://tempuri.org/" xmlns:ns2="vp">
                <SOAP-ENV:Header>
                    <ns2:MerchantIdentifier>'.$this->config->MerchantIdentifier.'</ns2:MerchantIdentifier>
                    <ns2:APIkey>'.$this->config->APIkey.'</ns2:APIkey>
                </SOAP-ENV:Header>
                <SOAP-ENV:Body>
                    '.$body.'
                </SOAP-ENV:Body>
            </SOAP-ENV:Envelope>';
        return $envelope;
    }  
    private function ConvertSoapXmlToResponseObject($method, $xml){
        $exception_key = "<s:Fault>";
        $is_exception = strpos($xml, $exception_key);

        $method_response = $method."Response";
        $method_result = $method."Result";
        $soap_envelope = "Envelope";
        $soap_body = "Body";

        $xml = $this->CleanXmlResponse($xml);
        $xmlObj = new XmlToArray($xml);
        $obj_array = $xmlObj->createArray();
        
        if($is_exception){
            $fault = "Fault";
            $xml_body = $obj_array[$soap_envelope][$soap_body][0];     
            $response = (object)$xml_body[$fault][0];
        }else{
            $xml_body = $obj_array[$soap_envelope][$soap_body][0];     
            if(is_array($xml_body[$method_response][0][$method_result][0]))
                $response = (object)$xml_body[$method_response][0][$method_result][0];
            else
                $response = (object)$xml_body[$method_response][0][$method_result];
        }
        
        return $response;
    }
    private function CleanXmlResponse($xml){
        $clean = $xml;
        $clean = str_replace(' xmlns:s="http://schemas.xmlsoap.org/soap/envelope/"', '', $clean);        
        $clean = str_replace(' xmlns="http://tempuri.org/"', '', $clean);
        $clean = str_replace(' xmlns:a="http://schemas.datacontract.org/2004/07/Oink.Services.Model" xmlns:i="http://www.w3.org/2001/XMLSchema-instance"', '', $clean);        
        $clean = str_replace(' i:nil="true"', '', $clean);
        $clean = str_replace('<s:', '<', $clean);          
        $clean = str_replace('</s:', '</', $clean);                  
        $clean = str_replace('<a:', '<', $clean);        
        $clean = str_replace('</a:', '</', $clean);       
        $clean = str_replace(' xmlns:a="http://schemas.microsoft.com/net/2005/12/windowscommunicationfoundation/dispatcher"', '', $clean);  
        $clean = str_replace(' xml:lang="en-US"', '', $clean);  
        $clean = str_replace(' xmlns="http://schemas.datacontract.org/2004/07/System.ServiceModel" xmlns:i="http://www.w3.org/2001/XMLSchema-instance"', '', $clean);  
        return $clean;
    }
    /**
     *
     * @param stdClass $result 
     * @throws Exception
     */
    private function CheckResponse($result){
        if(isset($result->faultcode)){
            throw new OinkException($result->faultstring);
        }
    }
}
?>
