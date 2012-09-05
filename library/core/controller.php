<?php

/**
 * @todo Controller Interface Class
 */
class Controller {
    protected $document;
    protected $registry;
    protected $config;
    protected $session;
    protected $layout;
    protected $layoutDir;
    protected $root;
    protected $template;
    protected $output;
    protected $router;
    protected $data = array();

    public function __construct($option = array()) {
        $this->root = Factory::getRoot();
        $this->registry = Factory::getRegistry();
        $this->config = Factory::getConfig();
        $this->router = Factory::getRouter();
        //$this->session = Factory::getSession();
        //$this->document = Factory::getDocument();
        
        $this->layout = 'default';
        $this->layoutDir = '/views';
        $this->template = '';
        $this->output = '';
    }

    public function __get($key) {
        return $this->registry->get($key);
    }

    public function __set($key, $value) {
        $this->registry->set($key, $value);
    }

    public function forward($route, $args = array()) {
        $controller = new ControllerAction($router);
        return $controller->run();
    }

    public function redirect($url, $status = 302) {
        header('Status: ' . $status);
        header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $url));
        exit();
    }

    public function render() {
        if (file_exists($this->template)) {
            extract($this->data);

            ob_start();

            include $this->template;

            $this->output = ob_get_contents();

            ob_end_clean();

            return $this->output;
        } else {
            trigger_error('Error: Could not load template ' . $this->template . '!');
            exit();
        }
    }

}

/**
 * @todo Action Controller Class
 */
class ActionController {
    /**
     * @var The module name match with current path
     */
    protected $_module;
    /**
     * @var The controller name match with current path
     */
    protected $_controller;
    /**
     * @var The action name match with current path
     */
    protected $_action;
    /**
     * @var The Application config object
     */
    protected $_config;
    /**
     * @var The Application router object
     */
    protected $_router;
    
    /**
     * @todo Get current router and init some variables
     */
    public function __construct(&$router) {
        $this->_router     = $router;
        $this->_config     = Factory::getConfig();
        $this->_module     = isset($router['module']) ? $router['module'] : $this->_config->get('module');
        $this->_controller = isset($router['controller']) ? $router['controller'] : $this->_config->get('controller');
        $this->_action     = isset($router['action']) ? $router['action'] : $this->_config->get('action');
    }

    /**
     * @todo Execute current router and return html
     */
    public function execute($data = null) {
        $class  = ucfirst($this->_module) . ucfirst($this->_controller) . 'Controller';
        $method = $this->_action . 'Action';
        
        // check if controller method exists
        if(method_exists($class, $method)) {
            // params pass to function
            $param = $this->_router;
            
            // remove router variable from param
            unset($param['module']);
            unset($param['controller']);
            unset($param['action']);
            
            // merge data with param pass to function
            if($data) {
                // if data is array, merge with param
                if(is_array($data)) {
                    $param = $param + $data;
                } else {
                    $param[] = $data;
                }
            }

            // create new instance of controller
            $instance = new $class();
            
            // call::controller->init();
            if(method_exists($class, 'init')) {
                call_user_func_array(array(&$instance, 'init'), $param);
            }
            
            // call::controller->permissionAccess();
            if(method_exists($class, 'permissionAccess')) {
                $access = $instance->permissionAccess();
                if(isset($access[$this->_action]) && $perm = $access[$this->_action]) {
                    // check user perm for access this action
                    if(!user::access($perm)) {
                        return $this->denied();
                    }
                }
            }
            
            $result = call_user_func_array(array(&$instance, $method), $param);
            $result = call_user_func_array(array(&$instance, 'render'), $param);

            return $result;

        } else {
            return $this->error();
        }
    }
    
    /**
     * @todo Forward to other action
     */
    public function forward($router, $param = null) {
        
        // Check the router before forward
        $router['module']     = isset($router['module']) ? $router['module'] : $this->_module;
        $router['controller'] = isset($router['controller']) ? $router['controller'] : $this->_controller;
        $router['action']     = isset($router['action']) ? $router['action'] : $this->_action;
        
        // Create new controller action
        $controller = new ControllerAction($router);
        
        // Return respond content by controller action
        return $controller->execute($param);
    }
    public function error($message = '') {
        $error = array(
            'module'     => 'error',
            'controller' => '',
            'action'     => 'error',
            'router'     => $this->_router,
        );            
        return $this->forward($error, $message);
    }
    public function denied($message = '') {
        $denied = array(
            'module'     => 'error',
            'controller' => '',
            'action'     => 'denied',
            'router'     => $this->_router,
        );            
        return $this->forward($denied, $message);        
    }
}

/**
 * @todo Front Controller Class
 */
class FrontController {
    
    protected $registry;
    protected $content = array();
    protected $error;

    public function __construct($registry) {
        $this->registry = $registry;
    }

    public function addPreAction($pre_action) {
        $this->pre_action[] = $pre_action;
    }

    public function dispatch($action, $error) {
        $this->error = $error;

        foreach ($this->pre_action as $pre_action) {
            $result = $this->execute($pre_action);

            if ($result) {
                $action = $result;

                break;
            }
        }

        while ($action) {
            $action = $this->execute($action);
        }
    }

    private function execute($action) {
        if (file_exists($action->getFile())) {
            require_once($action->getFile());

            $class = $action->getClass();

            $controller = new $class($this->registry);

            if (is_callable(array($controller, $action->getMethod()))) {
                $action = call_user_func_array(array($controller, $action->getMethod()), $action->getArgs());
            } else {
                $action = $this->error;

                $this->error = '';
            }
        } else {
            $action = $this->error;

            $this->error = '';
        }

        return $action;
    }

}
