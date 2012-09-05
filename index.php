<?php

/**
 * @package     Maxen™
 * @category    Maxen™ core
 * @uses        Event for hook : initEvent, routeEvent, 
 * @uses        Action for page execute
 */

/**
 * $theme->setOptions();
 * $theme->getRegions();
 * 
 */
$path = isset($_GET['route']) ? $_GET['route'] : '';
$path = trim(urldecode($path), '/');

$dir = dirname(__FILE__);

// Load framework core library
include $dir . '/library/core/config.php';
include $dir . '/library/core/registry.php';
include $dir . '/library/core/factory.php';
include $dir . '/library/core/common.php';
include $dir . '/library/core/path.php';
include $dir . '/library/core/router.php';
include $dir . '/library/core/database.php';
include $dir . '/library/core/session.php';
include $dir . '/library/core/controller.php';

// Set framework root directory
Factory::getRoot($dir);

// Init some system variables
$config = Factory::getConfig();
$router = Factory::getRouter();
$registry = Factory::getRegistry();

// Include all module match with current path
Module::loadModules();
// Call all module init hook
Module::invoke('init');     //call::initEvent
Module::invoke('route');    //call::routeEvent

// Get route match with current path
$routes = $config->get('routes', array());
$router->addConfig($routes);

$defaultRouter = array(
    'module'     => 'content',
    'controller' => 'index',
    'action'     => 'index',
);

$currentRouter = $router->route($path);
$currentRouter = $currentRouter + $defaultRouter;

$registry->set('router', $currentRouter);

$runner = new ControllerAction($currentRouter);
echo 'The router is :';
trace($currentRouter);
trace($runner->run());

//trace($config);

class ControllerAction {
    protected $_config;
    protected $_registry;
    protected $_module;
    protected $_controller;
    protected $_action;
    protected $_router;
    public function __construct(&$router) {
        $this->_router     = $router;
        $this->_config     = Factory::getConfig();
        $this->_registry   = Factory::getRegistry();
        $this->_module     = isset($router['module']) ? $router['module'] : $this->_config->get('module');
        $this->_controller = isset($router['controller']) ? $router['controller'] : $this->_config->get('controller');
        $this->_action     = isset($router['action']) ? $router['action'] : $this->_config->get('action');
    }

    public function run() {
        $class  = ucfirst($this->_module) . ucfirst($this->_controller) . 'Controller';
        $method = $this->_action . 'Action';
        
        // check if controller method exists
        if(method_exists($class, $method)) {
            // params pass to function
            $param = $this->_router;
            // remove router variable
            unset($param['module']);
            unset($param['controller']);
            unset($param['action']);
            // call the function
            $instance = new $class();
            
            if(method_exists($class, 'init')) {
                call_user_func_array(array(&$instance, 'init'), $param);
            }
            $result = call_user_func_array(array(&$instance, $method), $param);
            $result = call_user_func_array(array(&$instance, 'render'), $param);

            return $result;

        } else {
            $error = array(
                'module'     => 'error',
                'controller' => '',
                'action'     => 'error',
                'router'     => $this->_router,
            );            
            return $this->forward($error);
        }
    }
    
    public function forward($router, $param = array()) {
        $controller = new ControllerAction($router);
        return $controller->run();
    }
}

class Application {

    /**
     * @param The current module
     */
    public static $_module = 'default';

    /**
     * @param The current controller
     */
    public static $_controller = 'index';

    /**
     * @param The current action
     */
    public static $_action = 'Index';

    /**
     * @param The application router
     */
    public static $_router = NULL;

    /**
     * @param The current theme
     */
    public static $_theme = NULL;

    /**
     * @param The modules list
     */
    public static $_modules = array();

    /**
     * @param The application registry to container variable
     */
    public static $_registry = NULL;

    /**
     * @param The application config to store config data
     */
    public static $_config = NULL;

    /**
     * @param The application session
     */
    public static $_session;

    /**
     * @param The application database connection
     */
    public static $_db = NULL;
    
    /**
     * @var Environment variables
     */
    public static $_rootDir = '';
    public static $_configDir = '';
    public static $_coreDir = '';
    public static $_moduleDir = '';
    public static $_debug = false;
    public static $_time = 0;
    public static $_host = 'localhost';
    public static $_secure = false;
    public static $_instance = NULL;

    public static function setRoot($dir) {
        self::$_rootDir = $dir;
        self::$_coreDir = $dir . '/library/core';
        self::$_configDir = $dir . '/library/config';
        self::$_moduleDir = $dir . '/modules';
    }
    
    public static function init($option = array()) {
    }
    
    public static function router($path = null) {
        
    }

    /**
     * @todo Init application
     */
    public static function begin() {
        
    }

    public static function end() {
        
    }
    
    public static function run() {
        // Run module, controller, action
        $module = self::$_module;
        $controller = self::$_controller;
        $action = self::$_action;
        
        $class = ucfirst($module) . 'Controller';
        $method = ucfirst($action) . 'Action';
        
        if(method_exists($method, $class)) {
            
        } else {
            
        }
        
        // Load theme regions
    }
    
    /**
     * @todo Load framework library core
     */
    public static function loadLibrary() {
        $dir = Factory::getRoot() . '/library/core';
    }

    /**
     * @todo Load default config
     * @todo Load extended config match with current path
     */
    public static function loadConfig() {
        self::$_config->load(self::$_configDir . '/default.php');
    }

    /**
     * @todo Load modules match with current path
     */
    public static function loadModules() {
        $dir = Factory::getRoot() . '/modules';
        $config = Factory::getConfig();
        $modules = $config->get('modules', array());
        
        foreach($modules as $name => $module) {
            // If module route match with current path, include module
            foreach($module->controllers as $controller => $route) {
                if($route === '*' || $route === '' || path::match($route)) {
                    include $dir . '/' . $name . '/' . $controller . '.php';
                }
            }
        }
    }
    
    /**
     * @todo Application module hook call
     */
    public static function invoked() {
        
    }
    
    public static function getRouter() {
        return self::$_router;
    }

}

trace(get_included_files());
