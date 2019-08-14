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
        $user=$helper->getUser();
        $methods=$user->getPaymentMethods();
        if (!empty($methods)){
            foreach($methods as $method){
                if($method->getToken() <> null){
                    return $methods;
                }
            }
        }
        throw new Exception("Parent has no payment accounts available/activated. Please refer to Oink's dashboard and configure/activate one.");
        return;
    }
}