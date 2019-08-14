<?php
/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Block_Checkout_ParentConfirm_Payment
        extends Mage_Core_Block_Template
{
    public function getMethods(){
        $helper=Mage::helper("oink");
        try {
            $user=$helper->getUser();
            $methods=$user->getPaymentMethods();
            if (!empty($methods)){
                foreach($methods as $method){
                    if($method->getToken() <> null){
                        return $methods;
                    }
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}