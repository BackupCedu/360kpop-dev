<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class SystemController {
    public function init() {
        
    }
    
    public function indexAction() {
        $this->view->assign('a', 123);
        $this->view->assign();
    }
    
    public function permission() {
        
    }
}

?>
