<?php
/**
 * Provides all Data Transfer Objects, DTOs, which can be used for
 * moving data into & out of Oink services in a decoupled
 * fashion
 * 
 * @package Oink.Data.dtos
 */

/**
 * Result object used to transmit status of a web service call
 */
class dtoResultObject
{
    /**
    * Describes any exceptions in the PHP client or errors in the webservice
    * @var string
    * @access public
    */
    var $ErrorMessage;
    /**
    * Indicates if the web method was executed properly
    * @var bool
    * @access public
    */
    var $Status;
    /**
    * An encrypted authentication key which needs to be used for all subsequent transactions
    * @var guid
    * @access public
    */
    var $Token;
    /**
    * @var string
    * @access public
    */    
    var $TransactionStatus;
    /**
    * Value that determines a transaction from system of origin
    * @var string
    * @access public
    */    
    var $TransactionIdentifier;
    
    /**
    * Original XML response
    * @var string
    * @access public
    */    
    var $Xml;

}
/**
 * Oink user
 */
class dtoUser extends dtoResultObject
{
    var $UserType;
}
/**
 * Oink children
 */
class dtoChildren{
    var $Name;
    var $Token;
}
/**
 * Oink payment account
 */
class dtoPaymentAccount{
    var $Type;
    var $Name;
    var $Token;
    var $Url;
}
/**
 * Oink specific configuration elements
 */
class dtoPaymentGatewayConfiguration
{
    /**
    * Namespace for header properties
    * @var string
    * @access public
    */
    var $HeaderNamespace;
    /**
     * Header property for merchant identifier value
     */
    var $propMerchantIdentifier;
    /**
     * Header property for api key value
     */
    var $propApiKey;
    /**
     * URI for the Oink Transaction service
     */
    var $TransactionServiceEndpointAddress;
    /**
     * URI for the Oink Transaction service WSDL
     */
    var $TransactionServiceEndpointAddressWsdl;
    /**
     * URI for the Oink Parent service
     */
    var $ParentServiceEndpointAddress;
    /**
     * URI for the Oink Parent service WSDL
     */
    var $ParentServiceEndpointAddressWsdl;
    /**
     * Value required to be passed in all webservice calls for authentication.
     * Will be provided to the merchant by Oink
     */
    var $MerchantIdentifier;
    /**
     * Value required to be passed in all webservice calls for authentication
     * Will be provided to the merchant by Oink
     */
    var $APIkey;
    /**
     * The currency in which the transaction is being sent to Oink
     */
    var $Currency;
    /**
     * The desired default ship method that a merchant decides. There can only be 
     * one shipping method per merchant application.
     */
    var $DefaultShipmentMethod;

}
/**
 * Used to pass authentication credential to Oink services
 */
class dtoCredentials
{

    var $userName;
    var $password;
}
class dtoProfileInfo
{
    var $Age;
    var $Gender;
    var $ErrorMessage;
}
class dtoAddressRequestFormConfiguration
{
    var $PostAction;
    var $MerchantIdentifier;
    var $Url;
    var $ButtonHtml;
}
class dtoCheckoutFormConfiguration extends dtoAddressRequestFormConfiguration
{
	var $Description;
	var $Data;
	var $TransactionIdentifier;
	var $Amount;
	var $ExpiryDate;
	var $ChildIdentifier;
	var $ItemIdentifier;
	var $Currency;
	var $ErrorUrl;
	var $ParentIdentifier;
	var $GuestChildName;
	var $NotifyChild;
	var $DeliveryToChildAddress;
	var $PaymentAccountIdentifier;
	var $ExternalLoginFlag;
	var $Token;
	var $Value;
}
/**
 * Obtained from forms integration address request
 */
class dtoAddressRequest
{
    var $State;
    var $Zip;
    var $Token;
    var $MerchantIdentifier;
}
/**
 * Used to pass address information from Oink
 */
class dtoAddress
{

    var $Status;
    var $ErrorMessage;
    var $Address;
    var $City;
    var $State;
    var $Zip;
    var $Country;
    var $Phone;
    var $ParentName;
    var $ParentEmail;
    var $ChildName;
    /**
     * Method to serialize an address to Oink specific XML
     */
    public function ToXml()
    {

        $doc = new DOMDocument();

        $shipment = $this->getXmlElement($doc);

        $doc->formatOutput = true;
        return $doc->saveXML($shipment);
    }
    
    public function getXmlElement($doc){
        $shipment = $doc->createElement('shipment-address');
        $doc->appendChild($shipment);

        $elements = $this->_getXmlElements();

        foreach ($elements as $key => $content) {
            $newElementCDATA = $doc->createCDATASection($content);
            $newElement = $doc->createElement($key);
            $newElement->appendChild($newElementCDATA);
            $shipment->appendChild($newElement);
        }
        
        return $shipment;
    }

    protected function _getXmlElements()
    {
        return array(
            'address' => $this->Address,
            'zip' => $this->Zip,
            'city' => $this->City,
            'state' => $this->State,
            'country' => $this->Country,
            'phone' => $this->Phone,
            'name' => $this->ParentName,
            'attention-of' => $this->ChildName,
        );
    }

}
/**
 * The cart contains credit card charge information and product purchase information
 */
class dtoWishlist
{

    public function AddItem(dtoWishlistItem $item)
    {
        array_push($this->Items, $item);
    }

    var $Items = array();

    /**
     * Used to serialize shopping cart elements to Oink specific XML
     */
    public function ToXml()
    {
        $doc = new DOMDocument();
        $items = $doc->createElement('items');
        $doc->appendChild($items);

        foreach ($this->Items as $item) {
            $items->appendChild($item->getXmlNode($doc));
        }
        
        $doc->formatOutput = true;
        return $doc->saveXML($items);
    }
}


/**
 * The cart contains credit card charge information and product purchase information
 */
class dtoCart
{

    function __construct()
    {
        $this->ShipmentAddress = new dtoAddress();
    }

    public function AddItem(dtoCartItem $item)
    {
        array_push($this->Items, $item);
    }

    var $Currency;
    var $Total;
    var $ShippmentTotal;
    /**
     * This is the tax applied to a order. This is not a mandatory field
     */
    var $Tax;
    var $Cost;
    /**
     * This is the discount amount applied to an order
     */
    var $Discount;
    var $ShipmentAddress;
    var $Items = array();

    /**
     * Used to serialize shopping cart elements to Oink specific XML
     */
    public function ToXml()
    {
        $doc = new DOMDocument();

        $cart = $doc->createElement('cart');
        $cart->setAttribute("currency", $this->Currency);
        $cart->setAttribute("total",number_format($this->Total, 2, '.', ''));
        $doc->appendChild($cart);

        $cartShipment = $doc->createElement('cart-shipment');
        $cartShipment->setAttribute("total",number_format($this->ShippmentTotal, 2, '.', ''));
        $cart->appendChild($cartShipment);

        $elements = $this->_getXmlElements();

        foreach ($elements as $key => $total) {
            $newElement = $doc->createElement($key);
            $newElement->setAttribute("total", number_format($total, 2, '.', ''));
            $cartShipment->appendChild($newElement);
        }

        $shipmentAddress = $this->ShipmentAddress->getXmlElement($doc);
        $cartShipment->appendChild($shipmentAddress);

        $items = $doc->createElement('items');
        $cartShipment->appendChild($items);

        foreach ($this->Items as $item) {
            $items->appendChild($item->getXmlNode($doc));
        }
        
        $doc->formatOutput = true;
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $doc->saveXML($cart);
    }
    
    public function toEscapedXml()
    {
        return htmlspecialchars($this->ToXml(), ENT_QUOTES);
    }
    
    protected function _getXmlElements()
    {
        return array(
            'shipment-tax' => $this->Tax,
            'shipment-cost' => $this->Cost,
            'shipment-discount' => $this->Discount,
        );
    }
}


/**
 * The subscription contains the subscription period information and subscription cost
 */
class dtoSubscription
{
    var $Currency;
    var $Total;
    /**
     * This is the subscription expiration date
     */
    var $ExpirationDate;
    /**
     * This is the subscription period. An example could be "Monthly"
     */
    var $Period;

    /**
     * Used to serialize the subscription to Oink specific XML
     * Example : <subscription period="Weekly"><initial-cost currency="USD" value="12" /><expiry date="2012-12-05" /></subscription>
     */
    public function ToXml()
    {

        $doc = new DOMDocument();
        
        $subscription = $doc->createElement('subscription');
        $subscription->setAttribute("period", $this->Period);
        $doc->appendChild($subscription);
         
        $initialCost = $doc->createElement('initial-cost');
        $initialCost->setAttribute("currency", $this->Currency);
        $initialCost->setAttribute("value", $this->Total);
        $subscription->appendChild($initialCost);
        
        $expiry = $doc->createElement('expiry');
        $expiry->setAttribute("date", $this->ExpirationDate);
        $subscription->appendChild($expiry);
        
        $doc->formatOutput = true;
        return $doc->saveXML($subscription);

    }
    
    public function toEscapedXml()
    {
        return htmlspecialchars($this->ToXml(), ENT_QUOTES);
    }
}


/**
  This are the line items of an order which contain specific information about the product purchased.
 * "Items" are not mandatory and instances of an item can occur multiple times to represent multiple
 * products being purchased.
 */
class dtoCartItem
{
    /**
     *   
     * This is the name of a product. For example,
     * ======================================================
     * Lego Star Wars Plo Koon's Jedi Starfighter
     */
    var $Name;
    /**
    *                                                             
    * This is a brief description of a product. For example,
    * ======================================================
    * LEGO Star Wars Plo Koon's Jedi Starfighter (8093). Lead the search for General Grievous with Jedi Master Plo Koon! Jedi Master Plo Koon, leader of a Clone Army taskforce, scours the spacelanes for General Grievous' new superweapon, Malevolence, as seen in Star Wars: The Clone Wars. If the mission proves too dangerous, Plo Koon can eject from his starfighter and live to fight the Separatists another day! Set includes Jedi Master Plo Koon minifigure, new R7-D4 astromech droid and Jedi starfighter with ejection seat in the cockpit.
    * The LEGO Star Wars Plo Koon's Jedi Starfighter (8093) features:
    * Set contains 175 pieces
    * Includes Jedi Master Plo Koon minifigure and a new R7-D4 astromech droid
    * Plo Koon's starfighter features cockpit ejection seat
    * Plo Koon's Starfighter measures 10" (26 cm) long!  
    */    
    var $Description;
    /**
     * This is the unit price for a product.
     */
    var $Price;
    /**
     * These are the amount of units of a product which are being purchased.
     */
    var $Quantity;
    /**
     * This is the line item total. Equivalent to unit price multiplied by quantity.
     */
    var $Total;
    /**
     * Used to serialize line items to Oink specific XML
     */
    public function ToXml()
    {

        $doc = new DOMDocument();
        
        $item=  $this->getXmlNode($doc);
        $doc->appendChild($item);

        $doc->formatOutput = true;
        return $doc->saveXML($total);
    }
    
    public function toEscapedXml()
    {
        return htmlspecialchars($this->ToXml(), ENT_QUOTES);
    }
    
    public function getXmlNode($doc){
        $item = $doc->createElement('item');
        $item->setAttribute("total", $this->Total);

        $elements = $this->_getXmlElements();

        foreach ($elements as $key => $content) {
            $newElement = $doc->createElement($key);
            if(!empty($content)){
                $newElementCDATA = $doc->createCDATASection($content);
                $newElement->appendChild($newElementCDATA);
            }
            $item->appendChild($newElement);
        }
        return $item;
    }

    protected function _getXmlElements()
    {
        return array(
            'item-name' => $this->Name,
            'item-description' => $this->Description,
            'item-price' => $this->Price,
            'item-quantity' => $this->Quantity,
        );
    }

}
class dtoWishlistStatus
{
  var $Children;
  var $ErrorMessage;
  var $Status;
  var $Token;
  var $TransactionStatus;
}

class dtoWishlistItem
{
    /**
     * Item SKU
     */    
    var $Identifier;
    /**
     * The URL of the product on the merchant's site
     */    
    var $Url;
    /**
     * This is a brief description of a product. For example,
     */    
    var $Description;

    public function ToXml()
    {

        $doc = new DOMDocument();
        
        $item=  $this->getXmlNode($doc);
        $doc->appendChild($item);

        $doc->formatOutput = true;
        return $doc->saveXML($total);
    }
    
    public function toEscapedXml()
    {
        return htmlspecialchars($this->ToXml(), ENT_QUOTES);
    }
    
    public function getXmlNode($doc){
        $item = $doc->createElement('item');
        $elements = $this->_getXmlElements();

        foreach ($elements as $key => $content) {
            $newElementCDATA = $doc->createTextNode($content);
            $newElement = $doc->createElement($key);
            $newElement->appendChild($newElementCDATA);
            $item->appendChild($newElement);
        }
        return $item;
    }

    protected function _getXmlElements()
    {
        return array(
            'Identifier' => $this->Identifier,
            'Url' => $this->Url,
            'Description' => $this->Description
        );
    }
}
/**
 * This object contains the information ralted to the status of a transaction which Oink 
 * sends after a transaction is approved.
 */
class dtoTransactionStatus
{
    /**
     * The Oink id for this transaction
     */    
    var $id;
    /**
     * The current status of a transaction. The possible values are:
     *      Processed: The transaction was processed, the payment was processed.
     *      ApprovalPending: The transaction requires parent approval as the parent has already been notified and it will not be processed until the parent approved it.
     *      LimitsExceeded: The transaction could not be processed if the child exceeded the limits set by their parents.
     *      Error: A general error prevented the transaction from being processed. A message will be shown in the UI for the reason.
     */    
    var $Status;
    /**
     * The website url provided by the merchant
     */            
    var $Url;
    /**
     * Any error messages related to this transaction
     */        
    var $errorMessage;
    /**
     * The merchant identifier provided by Oink
     */        
    var $MerchantIdentifier;
    /**
     * The transaction identifier provided by the merchant to identify a transaction in their ecommerce system
     */        
    var $TransactionIdentifier;
    /**
     * The original description provided by the merchant
     */        
    var $Description;
    /**
     * The transaction amount
     */        
    var $Amount;
    /**
     * The transaction expiration date if provided by the merchant
     */        
    var $ExpiryDate;
    /**
     * The XML document describing the transaction if provided by the merchant
     */        
    var $Data;
    /**
     * Parent or guest address associated to the transaction
     */        
    var $Address;
    /**
     * Parent or guest city associated to the transaction
     */        
    var $City;
    /**
     * Parent or guest zip code associated to the transaction
     */        
    var $Zip;
    /**
     * Parent or guest state associated to the transaction
     */        
    var $State;
    /**
     * Parent or guest country associated to the transaction
     */        
    var $Country;
}

?>
