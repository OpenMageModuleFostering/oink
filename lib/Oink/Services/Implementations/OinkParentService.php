<?php

/**
 * @package Oink.Services.Implementations
 */
class OinkParentService implements IParentService{
    
    var $config;
    
    function __construct($config) {
        $this->config = $config;
    }
    
    private function GetSoapClient()
    {
            $client = new SOAPClient($this->config->ParentServiceEndpointAddressWsdl);
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
    
    public function AuthenticateParent($username, $password){
        $result_dto = new dtoResultObject();
        $result_dto->ErrorMessage = "SOAP call not executed.";
        $result_dto->Status = false;
        
        try {
            $client = $this->GetSoapClient();
            $params = array(
                'userName' => $username,
                'password' => $password,
            );
            $result = $client->AuthenticateParent($params);
            
            $result_dto->ErrorMessage = $result->AuthenticateParentResult->ErrorMessage;
            $result_dto->Token = $result->AuthenticateParentResult->Token;
            $result_dto->Status = $result->AuthenticateParentResult->Status;            
            
        } catch(Exception $e) {
            $result_dto->ErrorMessage = $e->getMessage();
        }
        return $result_dto;
    }
            
    
    public function GetChildProfiles($token){
        
      
        try {
            $client = $this->GetSoapClient();
            $params = array( 
                'token'=> $token,
            );
            $result = $client->GetChildProfiles($params);
            return $result;
        } catch(Exception $e) {
            echo $e->getMessage();
        }
   }
}

?>
