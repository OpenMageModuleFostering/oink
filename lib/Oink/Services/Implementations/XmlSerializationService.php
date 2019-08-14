<?php
/**
 * @package Oink.Services.Implementations
 */

    class XmlSerializationService implements ISerializationService
    {
        var $xmlResult;
   
        function __construct(){
            $rootNode = "vp";
            $this->xmlResult = new SimpleXMLElement("<$rootNode></$rootNode>");
        }
   
        private function iteratechildren($object,$xml){
            foreach ($object as $name=>$value) {
                if (is_string($value) || is_numeric($value)) {
                    $xml->$name=$value;
                } else {
                    $xml->$name=null;
                    $this->iteratechildren($value,$xml->$name);
                }
            }
        }
        
        private function SerializeCart(dtoCart $cart,$schemaPath){
			/*
			 * This is just for testing. We are now using dto.ToXml() in magento application
			 * */
            $xml = file_get_contents($schemaPath, true);
            return trim($xml, " ");
            /*
            $x = '<?xml version="1.0" encoding="utf-8" ?>';
            $x .= $cart->ToXml();
            return $x;
             * 
             */
        }
        private function SerializeGeneric($object){
            $this->iteratechildren($object,$this->xmlResult);
            return $this->xmlResult->asXML();                        
        }
        public function SerializeObject($object,$schemaPath){
            if(is_a($object, 'dtoCart')){
                return $this->SerializeCart($object,$schemaPath);
            }else{
                return $this->SerializeGeneric($object);
            }
        }
                
        public function DeserializeObject($serialized){
            
        }
        
    }
?>