<?php

/**
 * @package Oink.Services.Implementations
 */
class OinkFormService implements IFormService {

        private function _GenerateInputElement($name, $variable)
        {
            $input = "";
            if(isset($variable)){
                $value = $variable;
                $input = '<input type="hidden" name="'.$name.'" value="'.$variable.'" />';
            }
            echo $input;
        }
    
        public function GenerateAddressRequestForm($config)
        {
            ?>
            <form action="<?php echo $config->PostAction; ?>" method="post" id="login">
                <input type="hidden" name="MerchantIdentifier" value="<?php echo $config->MerchantIdentifier; ?>" />
                <input type="hidden" name="Url" value="<?php echo $config->Url; ?>" />
                <input type="hidden" name="Lookup" value="State;Zip" />
                <?php echo $config->ButtonHtml; ?>
            </form>
            <?php
        }

        public function GenerateCheckoutForm($config)
        {
            ?>
            <form action="<?php echo $config->PostAction; ?>" method="post" id="login" name="login">
                <?php 
                $this->_GenerateInputElement("MerchantIdentifier",$config->MerchantIdentifier);
                $this->_GenerateInputElement("Url", $config->Url);
                $this->_GenerateInputElement("Description", $config->Description);
                $this->_GenerateInputElement("Data", $config->Data);
                $this->_GenerateInputElement("TransactionIdentifier", $config->TransactionIdentifier);
                $this->_GenerateInputElement("Amount", $config->Amount);
                $this->_GenerateInputElement("Currency", $config->Currency);
                $this->_GenerateInputElement("ErrorUrl", $config->ErrorUrl);
                $this->_GenerateInputElement("ItemIdentifier", $config->ItemIdentifier);
                $this->_GenerateInputElement("ExpiryDate", $config->ExpiryDate);
                $this->_GenerateInputElement("ChildIdentifier", $config->ChildIdentifier);
                $this->_GenerateInputElement("ParentIdentifier", $config->ParentIdentifier);
                $this->_GenerateInputElement("GuestChildName", $config->GuestChildName);
                $this->_GenerateInputElement("NotifyChild", $config->NotifyChild);
                $this->_GenerateInputElement("DeliveryToChildAddress", $config->DeliveryToChildAddress);
                $this->_GenerateInputElement("PaymentAccountIdentifier", $config->PaymentAccountIdentifier);
                $this->_GenerateInputElement("ExternalLoginFlag", $config->ExternalLoginFlag);
                $this->_GenerateInputElement("Token", $config->Token);
                $this->_GenerateInputElement("Value", $config->Value);
                echo $config->ButtonHtml;
                ?>
            </form>
            <?php            
        }
}

?>
