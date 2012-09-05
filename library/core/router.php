<?php

/**
 * @todo Route
 *
 * @package    Maxen Framework
 * @subpackage Router
 */
class Route {

    /**
     * Default translator
     *
     * @var Zend_Translate
     */
    protected static $_defaultTranslator;

    /**
     * Translator
     *
     * @var Zend_Translate
     */
    protected $_translator;

    /**
     * Default locale
     *
     * @var mixed
     */
    protected static $_defaultLocale;

    /**
     * Locale
     *
     * @var mixed
     */
    protected $_locale;

    /**
     * Wether this is a translated route or not
     *
     * @var boolean
     */
    protected $_isTranslated = false;

    /**
     * Translatable variables
     *
     * @var array
     */
    protected $_translatable = array();
    protected $_urlVariable = ':';
    protected $_urlDelimiter = '/';
    protected $_regexDelimiter = '#';
    protected $_defaultRegex = null;

    /**
     * Holds names of all route's pattern variable names. Array index holds a position in URL.
     * @var array
     */
    protected $_variables = array();

    /**
     * Holds Route patterns for all URL parts. In case of a variable it stores it's regex
     * requirement or null. In case of a static part, it holds only it's direct value.
     * In case of a wildcard, it stores an asterisk (*)
     * @var array
     */
    protected $_parts = array();

    /**
     * Holds user submitted default values for route's variables. Name and value pairs.
     * @var array
     */
    protected $_defaults = array();

    /**
     * Holds user submitted regular expression patterns for route's variables' values.
     * Name and value pairs.
     * @var array
     */
    protected $_requirements = array();

    /**
     * Associative array filled on match() that holds matched path values
     * for given variable names.
     * @var array
     */
    protected $_values = array();

    /**
     * Associative array filled on match() that holds wildcard variable
     * names and values.
     * @var array
     */
    protected $_wildcardData = array();

    /**
     * Helper var that holds a count of route pattern's static parts
     * for validation
     * @var int
     */
    protected $_staticCount = 0;

    /**
     * Prepares the route for mapping by splitting (exploding) it
     * to a corresponding atomic parts. These parts are assigned
     * a position which is later used for matching and preparing values.
     *
     * @param string $route Map used to match with later submitted URL path
     * @param array $defaults Defaults for map variables with keys as variable names
     * @param array $reqs Regular expression requirements for variables (keys as variable names)
     * @param Zend_Translate $translator Translator to use for this instance
     */
    public function __construct($route, $defaults = array(), $reqs = array(), $translator = null, $locale = null) {
        $route = trim($route, $this->_urlDelimiter);
        $this->_defaults = (array) $defaults;
        $this->_requirements = (array) $reqs;
        $this->_translator = $translator;
        $this->_locale = $locale;

        if ($route !== '') {
            foreach (explode($this->_urlDelimiter, $route) as $pos => $part) {
                if (substr($part, 0, 1) == $this->_urlVariable && substr($part, 1, 1) != $this->_urlVariable) {
                    $name = substr($part, 1);

                    if (substr($name, 0, 1) === '@' && substr($name, 1, 1) !== '@') {
                        $name = substr($name, 1);
                        $this->_translatable[] = $name;
                        $this->_isTranslated = true;
                    }

                    $this->_parts[$pos] = (isset($reqs[$name]) ? $reqs[$name] : $this->_defaultRegex);
                    $this->_variables[$pos] = $name;
                } else {
                    if (substr($part, 0, 1) == $this->_urlVariable) {
                        $part = substr($part, 1);
                    }

                    if (substr($part, 0, 1) === '@' && substr($part, 1, 1) !== '@') {
                        $this->_isTranslated = true;
                    }

                    $this->_parts[$pos] = $part;

                    if ($part !== '*') {
                        $this->_staticCount++;
                    }
                }
            }
        }
    }

    /**
     * Matches a user submitted path with parts defined by a map. Assigns and
     * returns an array of variables on a successful match.
     *
     * @param string $path Path used to match against this routing map
     * @return array|false An array of assigned values or a false on a mismatch
     */
    public function match($path, $partial = false) {
        if ($this->_isTranslated) {
            $translateMessages = $this->getTranslator()->getMessages();
        }

        $pathStaticCount = 0;
        $values = array();
        $matchedPath = '';

        if (!$partial) {
            $path = trim($path, $this->_urlDelimiter);
        }

        if ($path !== '') {
            $path = explode($this->_urlDelimiter, $path);

            foreach ($path as $pos => $pathPart) {
                // Path is longer than a route, it's not a match
                if (!array_key_exists($pos, $this->_parts)) {
                    if ($partial) {
                        break;
                    } else {
                        return false;
                    }
                }

                $matchedPath .= $pathPart . $this->_urlDelimiter;

                // If it's a wildcard, get the rest of URL as wildcard data and stop matching
                if ($this->_parts[$pos] == '*') {
                    $count = count($path);
                    for ($i = $pos; $i < $count; $i+=2) {
                        $var = urldecode($path[$i]);
                        if (!isset($this->_wildcardData[$var]) && !isset($this->_defaults[$var]) && !isset($values[$var])) {
                            $this->_wildcardData[$var] = (isset($path[$i + 1])) ? urldecode($path[$i + 1]) : null;
                        }
                    }

                    $matchedPath = implode($this->_urlDelimiter, $path);
                    break;
                }

                $name = isset($this->_variables[$pos]) ? $this->_variables[$pos] : null;
                $pathPart = urldecode($pathPart);

                // Translate value if required
                $part = $this->_parts[$pos];
                if ($this->_isTranslated && (substr($part, 0, 1) === '@' && substr($part, 1, 1) !== '@' && $name === null) || $name !== null && in_array($name, $this->_translatable)) {
                    if (substr($part, 0, 1) === '@') {
                        $part = substr($part, 1);
                    }

                    if (($originalPathPart = array_search($pathPart, $translateMessages)) !== false) {
                        $pathPart = $originalPathPart;
                    }
                }

                if (substr($part, 0, 2) === '@@') {
                    $part = substr($part, 1);
                }

                // If it's a static part, match directly
                if ($name === null && $part != $pathPart) {
                    return false;
                }

                // If it's a variable with requirement, match a regex. If not - everything matches
                if ($part !== null && !preg_match($this->_regexDelimiter . '^' . $part . '$' . $this->_regexDelimiter . 'iu', $pathPart)) {
                    return false;
                }

                // If it's a variable store it's value for later
                if ($name !== null) {
                    $values[$name] = $pathPart;
                } else {
                    $pathStaticCount++;
                }
            }
        }

        // Check if all static mappings have been matched
        if ($this->_staticCount != $pathStaticCount) {
            return false;
        }

        $return = $values + $this->_wildcardData + $this->_defaults;

        // Check if all map variables have been initialized
        foreach ($this->_variables as $var) {
            if (!array_key_exists($var, $return)) {
                return false;
            } elseif ($return[$var] == '' || $return[$var] === null) {
                // Empty variable? Replace with the default value.
                $return[$var] = $this->_defaults[$var];
            }
        }

        //$this->setMatchedPath(rtrim($matchedPath, $this->_urlDelimiter));

        $this->_values = $values;

        return $return;
    }

}

class Routex {

    protected $_route;
    protected $_defaults = array();
    protected $_values = array();
    protected $_map = array();

    public function __construct($route, $default = array(), $map = array()) {
        $this->_route = $route;
        $this->_defaults = $default;
        $this->_map = $map;
    }

    /**
     * Matches a user submitted path with a previously defined route.
     * Assigns and returns an array of defaults on a successful match.
     *
     * @param  string $path Path used to match against this routing map
     * @return array|false  An array of assigned values or a false on a mismatch
     */
    public function match($path = '') {
        $regex = '#^' . $this->_route . '$#i';

        $values = array();
        $result = preg_match($regex, $path, $values);

        if ($result === 0) {
            return false;
        }

        // array_filter_key()? Why isn't this in a standard PHP function set yet? :)
        foreach ($values as $i => $value) {
            if (!is_int($i) || $i === 0) {
                unset($values[$i]);
            }
        }

        $this->_values = $values;

        $values = $this->_getMappedValues($values);
        $defaults = $this->_getMappedValues($this->_defaults, false, true);
        $return = $values + $defaults;

        return $return;
    }

    /**
     * Maps numerically indexed array values to it's associative mapped counterpart.
     * Or vice versa. Uses user provided map array which consists of index => name
     * parameter mapping. If map is not found, it returns original array.
     *
     * Method strips destination type of keys form source array. Ie. if source array is
     * indexed numerically then every associative key will be stripped. Vice versa if reversed
     * is set to true.
     *
     * @param  array   $values Indexed or associative array of values to map
     * @param  boolean $reversed False means translation of index to association. True means reverse.
     * @param  boolean $preserve Should wrong type of keys be preserved or stripped.
     * @return array   An array of mapped values
     */
    protected function _getMappedValues($values, $reversed = false, $preserve = false) {
        if (count($this->_map) == 0) {
            return $values;
        }

        $return = array();

        foreach ($values as $key => $value) {
            if (is_int($key) && !$reversed) {
                if (array_key_exists($key, $this->_map)) {
                    $index = $this->_map[$key];
                } elseif (false === ($index = array_search($key, $this->_map))) {
                    $index = $key;
                }
                $return[$index] = $values[$key];
            } elseif ($reversed) {
                $index = $key;
                if (!is_int($key)) {
                    if (array_key_exists($key, $this->_map)) {
                        $index = $this->_map[$key];
                    } else {
                        $index = array_search($key, $this->_map, true);
                    }
                }
                if (false !== $index) {
                    $return[$index] = $values[$key];
                }
            } elseif ($preserve) {
                $return[$key] = $value;
            }
        }

        return $return;
    }

}

class Router {

    /**
     * Array of routes to match against
     *
     * @var array
     */
    protected $_routes = array();

    /**
     * Currently matched route
     */
    protected $_currentRoute = null;

    /**
     * Array keys to use for module, controller, and action. Should be taken out of request.
     * @var string
     */
    protected $_moduleValid = false;
    protected $_moduleKey = 'module';
    protected $_controllerKey = 'controller';
    protected $_actionKey = 'action';

    /**
     * Add route to the route chain
     */
    public function addRoute($name, $route) {
        $this->_routes[$name] = $route;

        return $this;
    }

    /**
     * Add routes to the route chain
     */
    public function addRoutes($routes) {
        foreach ($routes as $name => $route) {
            $this->addRoute($name, $route);
        }

        return $this;
    }

    /**
     * @todo Add route from Config settings
     */
    public function addConfig($config, $section = null) {
        if ($section !== null) {
            $config = $config->{$section};
        }

        if ($config instanceof Config) {
            $config = $config->toArray();
        }

        foreach ($config as $name => $data) {
            $route = null;

            $type = isset($data['type']) ? $data['type'] : 'static';

            unset($data['type']);

            if ($type === 'static') {
                $route = new Route($name, $data);
            } else
            if ($type === 'regex') {
                $map = isset($data['map']) ? $data['map'] : array();

                unset($data['map']);

                $route = new Routex($name, $data, $map);
            }

            if ($route) {
                $this->addRoute($name, $route);
            }
        }

        return $this;
    }

    /**
     * @todo Get current route match with path
     */
    public function getRoute() {
        return $this->_currentRoute;
    }

    /**
     * Find a matching route to the current PATH_INFO and inject
     * returning values to the Request object.
     *
     * @throws Exception
     * @return Request object
     */
    public function route($path) {
        // Find the matching route
        $routeMatched = false;
        $values = array();
        foreach ($this->_routes as $name => $route) {
            if ($values = $route->match($path)) {
                $this->_currentRoute = $name;
                $routeMatched = true;
                break;
            }
        }

        if (false == $routeMatched) {
            $values = $this->routeDefault($path);
        }

        return $values;
    }

    private function routeDefault($path) {
        $values = array();
        $params = array();
        if ($path != '') {
            $path = explode('/', $path);

            if (count($path) && !empty($path[0])) {
                $values[$this->_moduleKey] = array_shift($path);
                $this->_moduleValid = true;
            }

            if (count($path) && !empty($path[0])) {
                $values[$this->_controllerKey] = array_shift($path);
            }

            if (count($path) && !empty($path[0])) {
                $values[$this->_actionKey] = array_shift($path);
            }

            if ($numSegs = count($path)) {
                for ($i = 0; $i < $numSegs; $i = $i + 2) {
                    $key = urldecode($path[$i]);
                    $val = isset($path[$i + 1]) ? urldecode($path[$i + 1]) : null;
                    $params[$key] = (isset($params[$key]) ? (array_merge((array) $params[$key], array($val))) : $val);
                }
            }
        }

        return $values + $params;
    }

}