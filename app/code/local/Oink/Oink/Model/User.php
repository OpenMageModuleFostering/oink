<?php
/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Model_User
        extends Mage_Core_Model_Abstract
{
    
    CONST USER_CODE_TYPE_CHILDREN="Child";
    CONST USER_CODE_TYPE_PARENT="Parent";

    protected $_token;
    protected $_address;
    protected $_arrayAddress;
    /**
     * Authenticate child and get the the result 
     * 
     * @param string $user Oink account user
     * @param string $password Oink account password
     * @return Oink_Oink_Model_User_Children|Oink_Oink_Model_User_Children
     */
    public function login($user, $password)
    {
        $paymentService = $this->_getHelper()->getOinkPaymentService();
        $credentials = Mage::helper("oink")->getDtoCredentials();
        $credentials->userName = $user;
        $credentials->password = $password;
        $auth = $paymentService->AuthenticateUser($credentials->userName, $credentials->password);
		/*
		 * When casting a string to bool php will consider empty strings false.
		 * 
         */
        if ((bool) $auth->Token) {
            if($auth->UserType==self::USER_CODE_TYPE_CHILDREN){
                $children=Mage::getModel("oink/user_children");
                $children->setToken($auth->Token);
                return $children;
            }elseif($auth->UserType==self::USER_CODE_TYPE_PARENT){
                $parent=Mage::getModel("oink/user_parent");
                $parent->setToken($auth->Token);
                return $parent;
            }
        }else{
            Mage::log($auth, null, "", true);
            Mage::throwException($auth->ErrorMessage);
        }
    }

    /**
     * Get address from Oink and change it to magento format. Optional flag to get street
     * as an array. 'street' must be an array for onepage checkout to accept an address.
     * @param dtoAddress
     * @param boolean
     * @return Varien_Object
     */
    public function getAddress($oinkAddress=null, $inArray=false)
    {
        if (is_null($this->_address)) {
            if(is_null($oinkAddress)){
                $oinkAddress = $this->getAddressDto();
            }
            $resource=Mage::getSingleton("core/resource");
            $connection=$resource->getConnection("core_read");
            $table=$resource->getTableName("directory_country_region");
            $query = "SELECT default_name,region_id FROM {$table} WHERE country_id='{$oinkAddress->Country}' and code='{$oinkAddress->State}'";
            $region = $connection->fetchRow($query);

            $this->_address = new Varien_Object(array(
                "street" => $oinkAddress->Address,
                "city" => $oinkAddress->City,
                "country_id" => $oinkAddress->Country,
                "firstname" => $this->_getFirstname($oinkAddress->ParentName),
                "lastname" => $this->_getLastname($oinkAddress->ParentName),
                "telephone" => (bool)$oinkAddress->Phone ? $oinkAddress->Phone : "11111111",
                "region" => $region["default_name"],
                "region_id" => $region["region_id"],
                "postcode" => $oinkAddress->Zip,
                "email" => $oinkAddress->ParentEmail,
            ));
        }

        if (is_null($this->_arrayAddress)) {
            $this->_arrayAddress = clone $this->_address;
            $this->_arrayAddress["street"] = array($oinkAddress->Address);
        }

        if ($inArray)
            return $this->_arrayAddress;
        return $this->_address;
    }

    /**
     * @return Oink_Oink_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper("oink");
    }
    /**
     * Get firstname from complete name
     * 
     * @param string $name
     * @return string
     */
    protected function _getFirstname($name)
    {
        if(is_null($name)){
            return "sds";
        }
        $_name = explode(" ", $name);
        return $_name[0];
    }
    /**
     * Get lastname from complete name
     * 
     * @param string $name
     * @return string
     */
    protected function _getLastname($name)
    {
        if(is_null($name)){
            return "sds";
        }
        $_name = explode(" ", $name);
        unset($_name[0]);
        return implode(" ", $_name);
    }
    /**
     * @return string
     */

    public function getRandomMail()
    {
        return uniqid() . "@gmail.com";
    }
    /**
     * @return string
     */
    public function getToken(){
        return $this->_token;
    }
    /**
     * @param $token string
     */
    public function setToken($token){
        $this->_token=$token;
        return $this;
    }


}

?>
