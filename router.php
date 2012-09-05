<?php

class router {

    /**
     * Whether or not to use default routes
     *
     * @var boolean
     */
    protected $_useDefaultRoutes = true;

    /**
     * Array of routes to match against
     *
     * @var array
     */
    protected $_routes = array();

    /**
     * Currently matched route
     *
     * @var Zend_Controller_Router_Route_Interface
     */
    protected $_currentRoute = null;

    /**
     * Global parameters given to all routes
     *
     * @var array
     */
    protected $_globalParams = array();

    /**
     * Separator to use with chain names
     *
     * @var string
     */
    protected $_chainNameSeparator = '-';

    /**
     * Determines if request parameters should be used as global parameters
     * inside this router.
     *
     * @var boolean
     */
    protected $_useCurrentParamsAsGlobal = false;

    /**
     * Add default routes which are used to mimic basic router behaviour
     *
     * @return Zend_Controller_Router_Rewrite
     */
    public function addDefaultRoutes() {
        if (!$this->hasRoute('default')) {
            $dispatcher = $this->getFrontController()->getDispatcher();
            $request = $this->getFrontController()->getRequest();

            require_once 'Zend/Controller/Router/Route/Module.php';
            $compat = new Zend_Controller_Router_Route_Module(array(), $dispatcher, $request);

            $this->_routes = array('default' => $compat) + $this->_routes;
        }

        return $this;
    }

    /**
     * Add route to the route chain
     *
     * If route contains method setRequest(), it is initialized with a request object
     *
     * @param  string                                 $name       Name of the route
     * @param  Zend_Controller_Router_Route_Interface $route      Instance of the route
     * @return Zend_Controller_Router_Rewrite
     */
    public function addRoute($name, Zend_Controller_Router_Route_Interface $route) {
        if (method_exists($route, 'setRequest')) {
            $route->setRequest($this->getFrontController()->getRequest());
        }

        $this->_routes[$name] = $route;

        return $this;
    }

    /**
     * Add routes to the route chain
     *
     * @param  array $routes Array of routes with names as keys and routes as values
     * @return Zend_Controller_Router_Rewrite
     */
    public function addRoutes($routes) {
        foreach ($routes as $name => $route) {
            $this->addRoute($name, $route);
        }

        return $this;
    }

    /**
     * Create routes out of Zend_Config configuration
     *
     * Example INI:
     * routes.archive.route = "archive/:year/*"
     * routes.archive.defaults.controller = archive
     * routes.archive.defaults.action = show
     * routes.archive.defaults.year = 2000
     * routes.archive.reqs.year = "\d+"
     *
     * routes.news.type = "Zend_Controller_Router_Route_Static"
     * routes.news.route = "news"
     * routes.news.defaults.controller = "news"
     * routes.news.defaults.action = "list"
     *
     * And finally after you have created a Zend_Config with above ini:
     * $router = new Zend_Controller_Router_Rewrite();
     * $router->addConfig($config, 'routes');
     *
     * @param  Zend_Config $config  Configuration object
     * @param  string      $section Name of the config section containing route's definitions
     * @throws Zend_Controller_Router_Exception
     * @return Zend_Controller_Router_Rewrite
     */
    public function addConfig(Zend_Config $config, $section = null) {
        if ($section !== null) {
            if ($config->{$section} === null) {
                require_once 'Zend/Controller/Router/Exception.php';
                throw new Zend_Controller_Router_Exception("No route configuration in section '{$section}'");
            }

            $config = $config->{$section};
        }

        foreach ($config as $name => $info) {
            $route = $this->_getRouteFromConfig($info);

            if ($route instanceof Zend_Controller_Router_Route_Chain) {
                if (!isset($info->chain)) {
                    require_once 'Zend/Controller/Router/Exception.php';
                    throw new Zend_Controller_Router_Exception("No chain defined");
                }

                if ($info->chain instanceof Zend_Config) {
                    $childRouteNames = $info->chain;
                } else {
                    $childRouteNames = explode(',', $info->chain);
                }

                foreach ($childRouteNames as $childRouteName) {
                    $childRoute = $this->getRoute(trim($childRouteName));
                    $route->chain($childRoute);
                }

                $this->addRoute($name, $route);
            } elseif (isset($info->chains) && $info->chains instanceof Zend_Config) {
                $this->_addChainRoutesFromConfig($name, $route, $info->chains);
            } else {
                $this->addRoute($name, $route);
            }
        }

        return $this;
    }

    /**
     * Remove a route from the route chain
     *
     * @param  string $name Name of the route
     * @throws Zend_Controller_Router_Exception
     * @return Zend_Controller_Router_Rewrite
     */
    public function removeRoute($name) {
        if (!isset($this->_routes[$name])) {
            require_once 'Zend/Controller/Router/Exception.php';
            throw new Zend_Controller_Router_Exception("Route $name is not defined");
        }

        unset($this->_routes[$name]);

        return $this;
    }

    /**
     * Remove all standard default routes
     *
     * @param  Zend_Controller_Router_Route_Interface Route
     * @return Zend_Controller_Router_Rewrite
     */
    public function removeDefaultRoutes() {
        $this->_useDefaultRoutes = false;

        return $this;
    }

    /**
     * Check if named route exists
     *
     * @param  string $name Name of the route
     * @return boolean
     */
    public function hasRoute($name) {
        return isset($this->_routes[$name]);
    }

    /**
     * Retrieve a named route
     *
     * @param string $name Name of the route
     * @throws Zend_Controller_Router_Exception
     * @return Zend_Controller_Router_Route_Interface Route object
     */
    public function getRoute($name) {
        if (!isset($this->_routes[$name])) {
            require_once 'Zend/Controller/Router/Exception.php';
            throw new Zend_Controller_Router_Exception("Route $name is not defined");
        }

        return $this->_routes[$name];
    }

    /**
     * Retrieve a currently matched route
     *
     * @throws Zend_Controller_Router_Exception
     * @return Zend_Controller_Router_Route_Interface Route object
     */
    public function getCurrentRoute() {
        if (!isset($this->_currentRoute)) {
            require_once 'Zend/Controller/Router/Exception.php';
            throw new Zend_Controller_Router_Exception("Current route is not defined");
        }
        return $this->getRoute($this->_currentRoute);
    }

    /**
     * Retrieve a name of currently matched route
     *
     * @throws Zend_Controller_Router_Exception
     * @return Zend_Controller_Router_Route_Interface Route object
     */
    public function getCurrentRouteName() {
        if (!isset($this->_currentRoute)) {
            require_once 'Zend/Controller/Router/Exception.php';
            throw new Zend_Controller_Router_Exception("Current route is not defined");
        }
        return $this->_currentRoute;
    }

    /**
     * Retrieve an array of routes added to the route chain
     *
     * @return array All of the defined routes
     */
    public function getRoutes() {
        return $this->_routes;
    }

    /**
     * Find a matching route to the current PATH_INFO and inject
     * returning values to the Request object.
     *
     * @throws Zend_Controller_Router_Exception
     * @return Zend_Controller_Request_Abstract Request object
     */
    public function route(Zend_Controller_Request_Abstract $request) {
        if (!$request instanceof Zend_Controller_Request_Http) {
            require_once 'Zend/Controller/Router/Exception.php';
            throw new Zend_Controller_Router_Exception('Zend_Controller_Router_Rewrite requires a Zend_Controller_Request_Http-based request object');
        }

        if ($this->_useDefaultRoutes) {
            $this->addDefaultRoutes();
        }

        // Find the matching route
        $routeMatched = false;

        foreach (array_reverse($this->_routes, true) as $name => $route) {
            // TODO: Should be an interface method. Hack for 1.0 BC
            if (method_exists($route, 'isAbstract') && $route->isAbstract()) {
                continue;
            }

            // TODO: Should be an interface method. Hack for 1.0 BC
            if (!method_exists($route, 'getVersion') || $route->getVersion() == 1) {
                $match = $request->getPathInfo();
            } else {
                $match = $request;
            }

            if ($params = $route->match($match)) {
                $this->_setRequestParams($request, $params);
                $this->_currentRoute = $name;
                $routeMatched = true;
                break;
            }
        }

        if (!$routeMatched) {
            require_once 'Zend/Controller/Router/Exception.php';
            throw new Zend_Controller_Router_Exception('No route matched the request', 404);
        }

        if ($this->_useCurrentParamsAsGlobal) {
            $params = $request->getParams();
            foreach ($params as $param => $value) {
                $this->setGlobalParam($param, $value);
            }
        }

        return $request;
    }

    /**
     * @todo
     *   check current path match with pattern
     * @param
     *   $pattern   string  pattern to check with path
     * @return
     *   $matches array
     */
    static function match($pattern = NULL) {
        static $matches = NULL;

        if ($pattern === NULL) {
            return $matches;
        }

        $pattern = str_replace('/', '\/', $pattern);
        $pattern = str_replace('%s', '([a-zA-Z0-9\-\.]+)', $pattern);
        $pattern = str_replace('%d', '(\d+)', $pattern);
        $pattern = '/^' . $pattern . '$/iu';

        $path = path::route();

        if (preg_match($pattern, $path, $matches)) {
            return $matches;
        }

        return false;
    }

    /**
     * @todo
     *   Get current query param
     *   The param parse by match function
     * @param
     *   $index     int     0 - n
     *   $default   ...
     * @return
     *   $value
     */
    static function param($index, $default = NULL) {
        static $params = NULL;

        if ($params === NULL) {
            $params = path::match();
        }
        if (isset($params[$index])) {
            return $params[$index];
        } else {
            return $default;
        }
    }

    /**
     * @todo 
     *   Get current query
     * @return 
     *   string current query path.
     */
    static function route() {
        if (isset($_GET['route'])) {
            return trim($_GET['route'], '/');
        }
        return '';
    }

    /**
     * Return a component of the current Drupal path.
     *
     * When viewing a page at the path "admin/content/types", for example, arg(0)
     * would return "admin", arg(1) would return "content", and arg(2) would return
     * "types".
     *
     * Avoid use of this function where possible, as resulting code is hard to read.
     * Instead, attempt to use named arguments in menu callback functions. See the
     * explanation in menu.inc for how to construct callbacks that take arguments.
     *
     * @param $index
     *   The index of the component, where each component is separated by a '/'
     *   (forward-slash), and where the first component has an index of 0 (zero).
     * @param $path
     *   A path to break into components. Defaults to the path of the current page.
     *
     * @return
     *   The component specified by $index, or NULL if the specified component was
     *   not found. If called without arguments, it returns an array containing all
     *   the components of the current path.
     */
    static function arg($index = NULL, $path = NULL) {
        static $arguments = NULL;

        if (!isset($path)) {
            $path = path::route();
        }
        if (!isset($arguments[$path])) {
            $arguments[$path] = explode('/', $path);
        }
        if (!isset($index)) {
            return $arguments[$path];
        }
        if (isset($arguments[$path][$index])) {
            return $arguments[$path][$index];
        }
    }

}

?>