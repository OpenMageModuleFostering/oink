<?php

/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Adminhtml_OinkController
        extends Mage_Adminhtml_Controller_Action
{
    public function testConnectionAction(){
		$TransactionServiceEndpointAddress = $this->getRequest()->getParam("transactionServiceEndpointAddress");
		$TransactionServiceEndpointAddressWsdl = $TransactionServiceEndpointAddress."?wsdl";
        $config=array(
			"TransactionServiceEndpointAddress" => $TransactionServiceEndpointAddress,
			"TransactionServiceEndpointAddressWsdl" => $TransactionServiceEndpointAddressWsdl,
            "MerchantIdentifier" => $this->getRequest()->getParam("merchantIdentifier"),
            "APIkey" => $this->getRequest()->getParam("apiKey"),
        );
        $paymentService = Mage::helper("oink")->getOinkPaymentService($config);
        $result=$paymentService->PingHeaders();
        $this->getResponse()->setBody($result);
    }


}

?>
