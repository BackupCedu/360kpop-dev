<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of index
 *
 * @author user
 */
class ContentFrontController extends Controller {
    public function initEvent() {
    }
    public function routeEvent($router) {
        $route = array();
        
        $route['default'] = new Route(':module/:controller/:action');
        $route['user'] = new Route('user/:action', array('module' => 'user', 'controller' => 'index'));
        $route['profile'] = new Route('user/:uid/:action', array('module' => 'user', 'controller' => 'profile'));
        $route['reg'] = new Routex('user/(\w+)/(\w+)', array('module' => 'user', 'controller' => 'profile'));
        
        $router->addRoute('user', $route['user']);
        $router->addRoute('profile', $route['profile']);
        $router->addRoute('profile', $route['profile']);
        $router->addRoute('default', $route['default']);
    }
    /**
     * @todo Home page
     */
    public function indexAction() {
        $this->template = dirname(__FILE__) . '/views/index.tpl.php';
    }
    /**
     * @todo Category page
     */
    public function cateAction($cid) {
        $this->template = dirname(__FILE__) . '/views/cate.tpl.php';
    }
    /**
     * @todo Node page
     */
    public function nodeAction($nid) {
        $this->template = dirname(__FILE__) . '/views/node.tpl.php';
    }
    
    /**
     * @todo Defined module permission
     */
    public function permission() {
        return array(
            'Access content',
            'Access category',
            'Access node',
        );
    }
    /**
     * @todo Set controller action permission
     */
    public function permissionAccess() {
        return array(
            'index' => array(
                'Access content',
            ),
            'cate' => array(
                'Access category',
            ),
            'node' => array(
                'Access node',
            ),
        );
    }
}

?>
