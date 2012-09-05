<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of front
 *
 * @author dino@maxen.vn
 */
class ErrorController extends Controller {
    public function errorAction($router) {
        $this->data['message'] = 'Could not find the module action.<br/>';
        $this->data['router'] = $router;
        $this->template = dirname(__FILE__) . '/views/error.tpl.php';
    }
    
    public function deniedAction($router) {
        $this->data['message'] = 'You dont have permission to access this module.<br/>';
        $this->data['router'] = $router;
        $this->template = dirname(__FILE__) . '/views/denied.tpl.php';
    }
}

?>