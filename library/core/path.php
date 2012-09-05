<?php

class path {

    /**
     * @todo
     *   check current path match with pattern
     * @param
     *   $pattern   string  pattern to check with path
     * @return
     *   $matches array
     */
    public static function match($pattern = NULL) {
        $matches = array();

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
    public static function param($index, $default = NULL) {
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
    public static function route() {
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
    public static function arg($index = NULL, $path = NULL) {
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