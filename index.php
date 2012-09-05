<?php

/**
 * @package     Maxen™
 * @category    Maxen™ core
 * @uses        Event for hook : initEvent, routeEvent, 
 * @uses        Action for page execute
 */

/**
 * front : content.module / front.module
 * admin : admin.module / controller: admin / front
 * content.module
 * content.admin.module
 * $theme->setOptions();
 * $theme->getRegions();s
 ***********************
 */

$module['route'];

// Load framework core library
include $dir . '/library/core/config.php';
include $dir . '/library/core/registry.php';
include $dir . '/library/core/factory.php';
include $dir . '/library/core/path.php';
include $dir . '/library/core/common.php';
include $dir . '/library/core/router.php';
include $dir . '/library/core/database.php';
include $dir . '/library/core/session.php';
include $dir . '/library/core/controller.php';

$dir = dirname(__FILE__);
$path = path::route();

// Set framework root directory
Factory::getRoot($dir);

// Init some system variables
$config = Factory::getConfig();
$router = Factory::getRouter();
$registry = Factory::getRegistry();

// Get route match with current path
$routes = $config->get('routes', array());
$router->addConfig($routes);

Module::loadModules();
Module::invoke('init');             //call::initEvent
Module::invoke('route', &$router);   //call::routeEvent

// Default router
$defaultRouter = array(
    'module'     => $config->get('module'),
    'controller' => $config->get('controller'),
    'action'     => $config->get('action'),
);

// Get current router
$currentRouter = $router->route($path);
$currentRouter = $currentRouter + $defaultRouter;

$registry->set('router', $currentRouter);

$controller = new ControllerAction($currentRouter);

trace($controller->run());
echo 'The router is :';
trace($currentRouter);

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
        $module     = self::$_module;
        $controller = self::$_controller;
        $action     = self::$_action;
        
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
     * @todo Application module hook call
     */
    public static function invoked() {
        
    }
    
    public static function getRouter() {
        return self::$_router;
    }

}

trace(get_included_files());
