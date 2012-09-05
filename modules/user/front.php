<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of index
 *
 * @author ROCK
 */
class UserFrontController extends Controller {
    /**
     * @todo This function call when application init
     */
    public function initEvent() {
    }
    /**
     * @todo This function call before router::route execute
     */
    public function routeEvent() {

    }    
    /**
     * @todo Init Module
     */
    public function init() {
        $this->layoutDir = realpath(dirname(__FILE__) . '/views');
        $this->template = realpath(dirname(__FILE__) . '/views/index.tpl.php');
    }
    /**
     * @todo Index action for user module
     */
    public function indexAction() {
        $this->data['error'] = '';
        $this->data['message'] = 'This is UserIndexAction content';
        $this->template = $this->layoutDir . '/index.tpl.php';
    }
    
    public function loginAction() {
        echo 'loginAction';
    }
    
    public function logoutAction() {
        echo 'logoutAction';
    }
    
    public function detailAction($uid) {
        echo $uid;
    }
    
    public function profileAction($alias = '', $uid = null) {
        echo $alias, '<br/>';
        echo $uid, '<br/>';
    }
}

?>
