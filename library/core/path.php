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
        static $matches = array();

        if ($pattern === NULL) {
            return $matches;
        }
        /**
         * Remove slash
         * Convert %s to string
         * Convert %d to number
         * Convert * to all character
         */
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = str_replace('%s', '([a-zA-Z0-9\-\.]+)', $pattern);
        $pattern = str_replace('%d', '(\d+)', $pattern);
        $pattern = str_replace('*', '(.*)', $pattern);
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
            return trim(urldecode($_GET['route']), '/');
        }
        return '';
    }

    /**
     * Return a component of the current Drupal path.
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