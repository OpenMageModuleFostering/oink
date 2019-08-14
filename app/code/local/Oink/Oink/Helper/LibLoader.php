<?php
/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Helper_LibLoader
extends Mage_Core_Helper_Abstract
{    
     /**
     *
     * @var string
     */
    const VIRTUALPIGGY_LIB_FOLDER_NAME="Oink";

    public function loadOinkLib(){
        $this->loadDtos();
        $this->loadInterfaces();
        $this->loadImplementations();
    }
    
    public function loadDtos(){
		/*
		 * VIRTUALPIGGY_LIB_FOLDER_NAME: constant relative to magento installation
		 * DS = directory separator (windows or linux sensitive), set in php.ini
		 * */
        $path=Mage::getBaseDir("lib").DS;
        $path.=self::VIRTUALPIGGY_LIB_FOLDER_NAME.DS."Data".DS."dtos.php";
        require_once $path;
    }
    
    public function loadInterfaces(){
        $path=Mage::getBaseDir("lib").DS;
        $path.=self::VIRTUALPIGGY_LIB_FOLDER_NAME.DS."Services".DS."Interfaces".DS;
        foreach (glob($path."*.php") as $key => $filename) {
            require_once $filename;
        }
    }
    
    public function loadImplementations(){
        $path=Mage::getBaseDir("lib").DS;
        $path.=self::VIRTUALPIGGY_LIB_FOLDER_NAME.DS."Services".DS."Implementations".DS;
        foreach (glob($path."*.php") as $key => $filename) {
            require_once $filename;
        }
    }
    
}
