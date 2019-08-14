<?php

/**
 * Description of Observer
 *
 * @author rterrani
 */
class Oink_MatrixrateIntegration_Model_Observer
{
    const MATRIXRATE_SHIPPING_METHOD_CODE="matrixrate";
    const MATRIXRATE_SHIPPING_METHODS_CONFIG_PATH="oink/matrixrate/shipping_methods";

    public function oinkAfterSetShippingMethod(Varien_Event_Observer $observer)
    {
        $shippingMethod = $observer->getShippingMethod();
        $availableShippingMethods = $observer->getAvailableShippingMethods();
        $shippingMethodCode=$shippingMethod->getCode();
        $_shippingMethod=explode("_",$shippingMethodCode);
        $carrier=array_shift($_shippingMethod);
        if ($carrier == self::MATRIXRATE_SHIPPING_METHOD_CODE) {
            $newShippingMethod = $this->_getAvailableMethod($availableShippingMethods,$shippingMethodCode);
            $shippingMethod->setCode($newShippingMethod);
        }
    }

    protected function _getAvailableMethod($availableShippingMethods)
    {
        $configuredShippingMethods = Mage::getStoreConfig(self::MATRIXRATE_SHIPPING_METHODS_CONFIG_PATH);
        $matrixrateShippingRates=$availableShippingMethods[self::MATRIXRATE_SHIPPING_METHOD_CODE];
        foreach ($configuredShippingMethods as $key => $shippingMethod) {
            foreach ($matrixrateShippingRates as $matrixrateKey => $matrixrateShippingRate) {
                if($matrixrateShippingRate->getMethodTitle()==$shippingMethod["method"]){
                    return $matrixrateShippingRate->getCode();
                }
            }
        }
        return $defaultShipmentMethod;
    }

}