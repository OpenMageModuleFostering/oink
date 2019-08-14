<?php

/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Helper_Data
        extends Mage_Core_Helper_Abstract
{
    /**
     *
     * @var string
     */
    const VIRTUALPIGGY_CONFIGURATION_PATH="oink/merchant_info";

    public function __construct()
    {
        Mage::helper("oink/libLoader")->loadOinkLib();
    }

    /**
     * Authenticate child and get the the result 
     * 
     * @param string $user Oink account user
     * @param string $password Oink account password
     * @return bool|Oink_Oink_Model_Children
     */
    public function authenticateChild($user, $password)
    {
        /*
         * magento requires a model for each entity
         * all logic associated with this class in model
         * Mage::getModel("oink/user_children") loads child model
         * */
        $children = Mage::getModel("oink/user_children");
        Mage::getSingleton("customer/session")->setOinkUser($children);
        return $children->login($user, $password);
    }

    /**
     * Authenticate child and get the the result 
     * 
     * @param string $user Oink account user
     * @param string $password Oink account password
     * @return bool|Oink_Oink_Model_Children
     */
    public function authenticateUser($userName, $password)
    {
        $user = Mage::getModel("oink/user");
        $user=$user->login($userName, $password);
        Mage::getSingleton("customer/session")->setOinkUser($user);
        return $user;
    }

    /**
     * Check if the Oink user is logged
     * 
     * @return bool
     */
    public function isUserLogged()
    {
        return (bool) $this->getUser();
    }

    /**
     * Get Oink user
     * 
     * @return Oink_Oink_Model_User
     */
    public function getUser()
    {
        return Mage::getSingleton("customer/session")->getOinkUser();
    }
    
    /**
     * Get Oink user type
     * 
     * @return string
     */
    public function getUserType()
    {
        $user=$this->getUser();
        if($user instanceof Oink_Oink_Model_User_Children){
            return Oink_Oink_Model_User::USER_CODE_TYPE_CHILDREN;
        }elseif($user instanceof Oink_Oink_Model_User_Parent){
            return Oink_Oink_Model_User::USER_CODE_TYPE_PARENT;
        }
    }

    /**
     * Get checkout quote instance by current session
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /*
     * Log to a specific oink log
     * 
     * @param mixed Message, array or object to log
     * 
     */

    public function log($message, $title=null)
    {
        if (!is_null($title)) {
            Mage::log($title, null, "oink.log", true);
        }
        Mage::log($message, null, "oink.log", true);
    }

    public function getExpiryTime()
    {
        $expiryTimeInDays = Mage::getStoreConfig("oink/merchant_info/order_expiration_time");
        return $expiryTimeInDays * 24 * 60 * 60;
    }

    /**
     * @param array $config Override saved config
     * @return OinkPaymentService
     */
    public function getOinkPaymentService($config=array())
    {
        return new OinkPaymentService($this->getOinkConfig($config));
    }

    /**
     * @return OinkParentService
     */
    public function getOinkParentService()
    {
        return new OinkParentService($this->getOinkConfig());
    }

    /**
     * @return XmlSerializationService
     */
    public function getXmlSerializationService()
    {
        return new XmlSerializationService();
    }

    /**
     * @return dtoCredentials
     */
    public function getDtoCredentials()
    {
        return new dtoCredentials();
    }

    /**
     * @return dtoCart
     */
    public function getDtoCart()
    {
        return new dtoCart();
    }

    /**
     * @return dtoCartItem
     */
    public function getCartItemDto()
    {
        return new dtoCartItem();
    }

    /**
     * Get Oink payment gateway configuration filled with the
     * configuration from magento
     * 
     * @param array $override
     * @return dtoPaymentGatewayConfiguration
     */
    public function getOinkConfig($override)
    {
        $mageConfig = Mage::getStoreConfig(self::VIRTUALPIGGY_CONFIGURATION_PATH);
        $mageConfig["ParentServiceEndpointAddressWsdl"]=$mageConfig["ParentServiceEndpointAddress"]."?wsdl";
        $mageConfig["TransactionServiceEndpointAddressWsdl"]=$mageConfig["TransactionServiceEndpointAddress"]."?wsdl";
        $config = new dtoPaymentGatewayConfiguration();
        foreach ($config as $key => $value) {
            $config->$key = isset($override[$key]) ? $override[$key] : $mageConfig[$key];
        }
        return $config;
    }

    /**
     * Get the checkout button as html
     * 
     * @return string
     */
    public function getCheckoutButtonHtml($data=array())
    {
        return Mage::app()->getLayout()->createBlock("oink/checkout_button","",$data)->toHtml();
    }

    public function formatXmlString($xml)
    {

        // add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
        $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);

        // now indent the tags
        $token = strtok($xml, "\n");
        $result = ''; // holds formatted version as it is built
        $pad = 0; // initial indent
        $matches = array(); // returns from preg_matches()
        // scan each line and adjust indent based on opening/closing tags
        while ($token !== false) :

            // test for the various tag states
            // 1. open and closing tags on same line - no change
            if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) :
                $indent = 0;
            // 2. closing tag - outdent now
            elseif (preg_match('/^<\/\w/', $token, $matches)) :
                $pad--;
            // 3. opening tag - don't pad this one, only subsequent tags
            elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
                $indent = 1;
            // 4. no indentation needed
            else :
                $indent = 0;
            endif;

            // pad the line with the required number of leading spaces
            $line = str_pad($token, strlen($token) + $pad, ' ', STR_PAD_LEFT);
            $result .= $line . "\n"; // add to the cumulative result, with linefeed
            $token = strtok("\n"); // get the next token
            $pad += $indent; // update the pad size for subsequent lines    
        endwhile;

        return $result;
    }

}