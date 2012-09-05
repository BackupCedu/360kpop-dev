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

Application::init($dir);
Application::run();

class Application {
    public static function init($dir) {
        /**
         * @todo Init framework core
         */
        $rootDir  = Factory::getRoot($dir);
        $config   = Factory::getConfig();
        $router   = Factory::getRouter();
        $registry = Factory::getRegistry();
        
        /**
         * @todo Load routes
         */
        $routes = Factory::getConfig()->get('routes');
        $router->addConfig($routes);
        
        /**
         * @todo Load modules
         */
        Module::loadModules();
        Module::invoke('init');             //call::initEvent
        Module::invoke('route', &$router);  //call::routeEvent
        
        // Default router
        $default = array(
            'module'     => $config->get('module'),
            'controller' => $config->get('controller'),
            'action'     => $config->get('action'),
        );
        
        $path = path::route();
        // Get current router
        $current = $router->route($path);
        $current = $current + $default;

        $registry->set('router', $current);

        $controller = new ActionController($current);

        $content = $controller->execute();
        
        $frontController = new FrontController();
        $frontController->setContent($content);
        $frontController->loadRegion();
        $frontController->loadTemplate();
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
        
        // Load theme regions
    }
}

// trace(get_included_files());
