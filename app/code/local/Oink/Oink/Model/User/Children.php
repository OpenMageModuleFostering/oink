<?php
/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Model_User_Children
        extends Oink_Oink_Model_User
{

    protected $_token;
    protected $_address;
    /**
     * Authenticate child and get the the result 
     * 
     * @param string $user Oink account user
     * @param string $password Oink account password
     * @return Oink_Oink_Model_Children
     */
    public function login($user, $password)
    {
        $paymentService = $this->_getHelper()->getOinkPaymentService();
        $credentials = Mage::helper("oink")->getDtoCredentials();
        $credentials->userName = $user;
        $credentials->password = $password;
        $auth = $paymentService->AuthenticateChild($credentials->userName, $credentials->password);
		/*
		 * When casting a string to bool php will consider empty strings false.
		 * */
        if ((bool) $auth->Token) {
            $this->_token = $auth->Token;
            return $this;
        }else{
            Mage::log($auth, null, "", true);
            return null;
        }
    }
    /**
     * Get address from Oink
     * 
     * @return dtoAddress
     */
    public function getAddressDto(){
        $paymentService = $this->_getHelper()->getOinkPaymentService();
        return $paymentService->GetChildAddress($this->_token);
    }
}

?>
