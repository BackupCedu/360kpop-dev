<?php

/**
 * Maxen Framework
 *
 * @category   Core
 * @package    Registry
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Registry.php 23775 2011-03-01 17:25:24Z ralph $
 */
final class Registry {

    private $data = array();

    public function get($key, $default = NULL) {
        return (isset($this->data[$key]) ? $this->data[$key] : $default);
    }

    public function set($key, $value) {
        $this->data[$key] = $value;
    }

    public function has($key) {
        return isset($this->data[$key]);
    }

    /**
     * Constructs a parent ArrayObject with default
     * ARRAY_AS_PROPS to allow acces as an object
     *
     * @param array $array data array
     * @param integer $flags ArrayObject flags
     */
    public function __construct($array = array()) {
        $this->data = $array;
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function __set($name, $value) {
        return $this->set($name, $value);
    }

}