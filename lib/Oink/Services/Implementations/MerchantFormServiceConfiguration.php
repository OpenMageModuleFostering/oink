<?php

class MerchantFormServiceConfiguration implements IFormServiceConfiguration
{
        public function GetAddressRequestFormConfiguration(){
            $config = new dtoAddressRequestFormConfiguration();
            $config->MerchantIdentifier = "be8d78b5-a156-4548-a00d-d3d4386f7426";
            $config->PostAction = "https://localhost:8181/FormPost/Submit";
            $config->Url = "http://localhost/vpintegration/trunk/PHP/Research/generic/checkout-forms-dynamicshipping.php";
            $config->ButtonHtml = '<input type="submit" value="Calculate Shipping" class="magentobutton" />';
            return $config;
        }
        public function GetPreAuthorizedCheckoutFormConfiguration(){
            $config = new dtoCheckoutFormConfiguration();
            $config->PostAction = "https://localhost:8181/FormPost/Submit";
            $config->MerchantIdentifier = "be8d78b5-a156-4548-a00d-d3d4386f7426";
            $config->Url = "http://localhost/vpintegration/trunk/PHP/Research/generic/checkout-forms-confirmation.htm";
            $config->ButtonHtml = '<input type="submit" value="Place Order" class="magentobutton" />';
            $config->Description = "testdesc";
            $config->TransactionIdentifier = "ID:1234567";
            $config->Amount = "13.13";
            $config->Currency = "USD";
            $config->ErrorUrl = "http://www.playdin.com";
            return $config;            
        }
        public function GetCheckoutFormConfiguration(){
            $config = new dtoCheckoutFormConfiguration();
            $config->PostAction = "https://localhost:8181/Checkout/Submit";
            $config->MerchantIdentifier = "be8d78b5-a156-4548-a00d-d3d4386f7426";
            $config->Url = "http://localhost/vpintegration/trunk/PHP/Research/generic/checkout-forms-confirmation.htm";
            $config->ButtonHtml = '<input type="submit" value="Place Order" class="magentobutton" />';
            $config->Description = "testdesc";
            //$config->Data = "";
            $config->TransactionIdentifier = "ID:1234567";
            $config->Amount = "13.13";
            //$config->ExpiryDate = "";
            //$config->ChildIdentifier = "";
            //$config->ItemIdentifier = "";
            $config->Currency = "USD";
            $config->ErrorUrl = "http://www.playdin.com";
            //$config->ParentIdentifier = "";
            //$config->GuestChildName = "";
            //$config->NotifyChild = "";
            //$config->DeliveryToChildAddress = "";
            //$config->PaymentAccountIdentifier = "";
            //$config->ExternalLoginFlag = "";
            //$config->Token = "";
            //$config->Value = "";
            return $config;
        }
    
}
?>
