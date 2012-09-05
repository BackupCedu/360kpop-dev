<?php

abstract class Controller {
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
    protected $children = array();

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

?>