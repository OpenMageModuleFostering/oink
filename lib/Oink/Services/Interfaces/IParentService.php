<?php
/**
 * @package Oink.Services.Interfaces
 */
    interface IParentService
    {
        public function AuthenticateParent($username, $password);
        public function GetChildProfiles($token);
    }
?>
