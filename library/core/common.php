<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @todo Module hook Event
 * @package Maxen™
 * @category Common
 *
 * @author dino@maxen.vn
 */
abstract class Module {

    /**
     * Invoke a hook in all enabled modules that implement it.
     *
     * @param $hook
     *   The name of the hook to invoke.
     * @param ...
     *   Arguments to pass to the hook.
     * @return
     *   An array of return values of the hook implementations. If modules return
     *   arrays from their implementations, those are merged into one array.
     */
    static function invoke() {
        $args = func_get_args();
        $hook = array_shift($args);
        $method = $hook . 'Event';
        $return = array();
        $modules = self::getModules();
        // Duyet qua danh sach module
        foreach ($modules as $name => $module) {
            // Duyet qua danh sach controller cua module
            foreach ($module->controllers as $controller => $route) {
                $class = ucfirst($name) . ucfirst($controller) . 'Controller';
                if (method_exists($class, $method)) {
                    $result = call_user_func_array(array($class, $method), $args);
                    if (isset($result) && is_array($result)) {
                        $return = array_merge_recursive($return, $result);
                    } else if (isset($result)) {
                        $return[] = $result;
                    }
                }
            }
        }

        return $return;
    }

    /**
     * @todo Get list module
     */
    public static function getModules() {
        return Factory::getConfig()->get('modules', array());
    }
    
    /**
     * @todo Load modules match with current path
     */
    public static function loadModules() {
        $dir = Factory::getRoot() . '/modules';
        $config = Factory::getConfig();
        $modules = Module::getModules();
        
        foreach($modules as $name => $module) {
            // If module route match with current path, include module
            foreach($module->controllers as $controller => $route) {
                if($route === '*' || $route === '' || path::match($route)) {
                    include $dir . '/' . $name . '/' . $controller . '.php';
                }
            }
        }
    }

}

function trace($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}