<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include('./libraries/core/router.php');


$path = isset($_GET['route']) ? $_GET['route'] : '';
$path = trim(urldecode($path), '/');

echo 'Path : ' . $path . '<br />';

$route = array();

$route['default'] = new RouteBasic(':module/:controller/:action');
$route['user'] = new RouteBasic('user/:action', array('module' => 'user', 'controller' => 'index'));
$route['profile'] = new RouteBasic('user/:uid/:action', array('module' => 'user', 'controller' => 'profile'));
$route['reg'] = new RouteRegex('user/(\w+)/(\w+)', array('module' => 'user', 'controller' => 'profile'));

$router = new Router();

$router->addRoute('default', $route['default']);
$router->addRoute('user', $route['user']);
$router->addRoute('profile', $route['profile']);
$router->addRoute('profile', $route['profile']);
// user/%/%/%
// user/%s/%d
// user/(\d)/ ...
// Application::addRoute('user/:task/:user', array(), array(1=>'task'));
// Check router for get module
// Load plugin
//$router->addRoute('category', $route['category']);
//$router->addRoute('post', $route['post']);
//$router->addRoute('user', $route['user']);

trace($router->route($path));

function trace($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

/**
 * @todo : Application main class
 * @todo : After get module action
 * @todo : Load module and init module
 * @todo : RenderinWQVmodule content
 * @todo : Load theme, load theme region, load region content
 * @todo : Rendering site content by theme
 * @todo : Compress content and return
 * @todo : Config for domain
 */
class Application {
	/**
	 * @param The current module
	 */
	protected $_module = 'default';
        
        /**
	 * @param The current controller
	 */
	protected $_controller = 'index';

	/**
	 * @param The current action
	 */
	protected $_action = 'Index';

	/**
	 * @param The application router
	 */
	protected $_router = NULL;

	/**
	 * @param The current theme
	 */
	protected $_theme = NULL;

	/**
	 * @param The plugins list
	 */
	protected $_plugins = array();

	/**
	 * @param The themes list
	 */
	protected $_themes = array();

	/**
	 * @param The modules list
	 */
	protected $_modules = array();

	/**
	 * @param The application registry to container variable
	 */
	protected $_registry = NULL;

	/**
	 * @param The application config to store config data
	 */
	protected $_config = NULL;

	/**
	 * @param The application session
	 */
	protected $_session;

	/**
	 * @param The application database connection
	 */
	protected $_db = NULL;
        
        protected $_rootDir = '';
        protected $_configDir = '';
        protected $_debug = false;
        
	public static $time = 0;
	public static $host = 'localhost';
	public static $secure = false;
        
        public static $_instance = NULL;

	public function __construct() {
        }

	public static function getInstance() {

	}

	public static function getRegistry() {
		return self::$_instance->$_registry;
	}

	public static function getConfig() {
		return self::$_instance->$_config;
	}

	public static function getTheme() {

	}

	public static function getPlugins() {

	}

	public static function getModules() {

	}

	public static function getRouter() {

	}

	public static function getRoutes() {

	}

	public static function getDbo() {

	}

	public static function addRoute() {

	}

	public static function addRouterRegx() {

	}

	public static function addRouteStatic() {

	}

	public static function run() {

	}
        
        public function loadDefaultConfig() {
            return array(
                'debug' => false,
                'title' => 'Maxen™',
                'dir' => '',
                'baseUrl' => '',
                'db' => array(
                    
                ),
                'cache' => array(
                    
                ),
                'cookie' => array(
                    
                ),
                'theme' => array(
                    
                ),
                ''
            );
        }
}

class UserHelper extends Helper {
	public static function getErrorMessage() {

	}

	public static function setErrorMessage() {

	}

	public static function getData() {

	}
}

UserHelper::getErrorMessage('');
UserHelper::setErrorMessage('');

class Error {
	public function errorAction() {
 
	}

	public function deniedAction() {

	}
}

