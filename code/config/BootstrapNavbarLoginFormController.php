<?php

/**
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class BootstrapNavbarLoginFormController extends Extension {
    
    private static $allowed_actions = array(
        'BootstrapNavbarLoginForm',
        'BootstrapNavbarModalLoginForm'
    );
    
    public function BootstrapNavbarLoginForm(){
        return BootstrapNavbarLoginForm::create($this->owner, "BootstrapNavbarLoginForm");
    }
    
    public function BootstrapNavbarModalLoginForm(){
        return BootstrapNavbarModalLoginForm::create($this->owner, "BootstrapNavbarModalLoginForm");
    }
}