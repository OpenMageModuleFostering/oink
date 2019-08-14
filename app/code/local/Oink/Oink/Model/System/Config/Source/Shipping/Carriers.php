<?php
/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Model_System_Config_Source_Shipping_Carriers
{
    protected $_options;
    /**
     *
     * @param   bool $isMultiselect
     * @return  array
     */
    public function toOptionArray($isMultiselect)
    {
        if (!$this->_options) {
            $this->_options = $this->getShippingOptions();
        }
        $options = $this->_options;
        if(!$isMultiselect){
            array_unshift($options, array('value'=>'', 'label'=>''));
        }

        return $options;
    }
    /**
     * Get active shipping methods
     *
     * @return  array
     */
    public function getShippingOptions(){
        $carriersRaw = Mage::getSingleton('shipping/config')->getAllCarriers();
        $config=Mage::getConfig();
        $carriers=$config->getNode("default/carriers")->asArray();
        $options=array();
        foreach ($carriers as $carrierCode => $carrier) {
            if(!(bool)$carrier["active"]){
                continue;
            }
            if(isset($carrier["name"])){
                $label=$carrier["name"];
            }elseif(isset($carrier["title"])){
                $label=$carrier["title"];
            }else{
                continue;
            }
            if(isset($carrier["allowed_methods"])){
                $carrierAllowedMethods=$this->getCarrierAllowedMethods($carrier,$carrierCode);
                $options[]=array("label"=>$label,"value"=>$carrierAllowedMethods);
            }elseif($carrierCode == "tablerate"){
                $options[]=array("label"=>$label,"value"=>$carrierCode."_".'bestway');                
            }else{
                $options[]=array("label"=>$label,"value"=>$carrierCode."_".$carrierCode);
            }
        }
        return $options;
    }
    /**
     * Get active shipping methods from a carrier
     *
     * @param array $carrier Shipping method instance
     * @param string $carrier Shipping method code
     * @return  array 
     */
    public function getCarrierAllowedMethods($carrier,$carrierCode){
        $carriersConfigGroup=Mage::getSingleton('adminhtml/config')->getSection("carriers")->groups;
        $carrierAllowedMethodsCodes=explode(",",$carrier["allowed_methods"]);
        $carrierMethodsModelName=(string)$carriersConfigGroup->$carrierCode->fields->allowed_methods->source_model;
        $carrierMethodsModel=Mage::getModel($carrierMethodsModelName);
        $carrierMethods=$carrierMethodsModel->toOptionArray();
        $carrierAllowedMethods=array();
        foreach ($carrierMethods as $method) {
            if(in_array($method["value"], $carrierAllowedMethodsCodes)){
                $method["value"]=$carrierCode."_".$method["value"];
                $carrierAllowedMethods[]=$method;
            }
        }
        return $carrierAllowedMethods;
    }
}
