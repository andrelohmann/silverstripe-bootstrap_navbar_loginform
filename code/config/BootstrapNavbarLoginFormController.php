<?php

/**
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class BootstrapNavbarLoginFormController extends Extension {
    
    private static $allowed_actions = array(
        'BootstrapNavbarLoginForm'
    );
    
    public function BootstrapNavbarLoginForm(){
        $LoginForm = Object::create("BootstrapNavbarLoginForm", $this->owner, "BootstrapNavbarLoginForm");
        return $LoginForm;
    }
}