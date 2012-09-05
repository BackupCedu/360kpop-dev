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
        $route = array();
        
        $route['default'] = new Route(':module/:controller/:action');
        $route['user'] = new Route('user/:action', array('module' => 'user', 'controller' => 'index'));
        $route['profile'] = new Route('user/:uid/:action', array('module' => 'user', 'controller' => 'profile'));
        $route['reg'] = new Route('user/(\w+)/(\w+)', array('module' => 'user', 'controller' => 'profile'));
        
        $router = Factory::getRouter();
        
        $router->addRoute('user', $route['user']);
        $router->addRoute('profile', $route['profile']);
        $router->addRoute('profile', $route['profile']);
        $router->addRoute('default', $route['default']);
    }    
    /**
     * @todo Init Module
     */
    public function init() {
        $action = $this->registry->get('router')->action; trace($action);
        $this->layoutDir = realpath(dirname(__FILE__) . '/views');
    }
    /**
     * @todo Index action for user module
     */
    public function indexAction() {
        $this->data['error'] = '';
        $this->data['message'] = 'This is IndexAction content';
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
