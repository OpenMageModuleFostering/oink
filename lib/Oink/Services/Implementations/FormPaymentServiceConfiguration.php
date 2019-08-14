<?php
/**
 * @package Oink.Services.Implementations
 */
class FormPaymentServiceConfiguration implements IPaymentServiceConfiguration
{
    public function GetServiceConfiguration()
    {
        $config = new dtoPaymentGatewayConfiguration();
        return $config;

    }
}
?>
