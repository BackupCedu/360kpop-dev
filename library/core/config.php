<?php

/**
 * Maxen Framework
 * 
 * @category   Maxen
 * @package    Config
 * @copyright  Copyright (c) 2005-2011 Maxen™ Ltd. (http://www.maxen.vn)
 * @license    none
 */
class Config implements ArrayAccess, Iterator {

    private $_index = 0;
    private $_count = 0;
    private $_data  = array();

    /**
     * @todo Retrieve a value and return $default if there is no element set.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed / object
     */
    public function get($key, $default = null, $object = true) {
        if (isset($this->_data[$key])) {
            if ($object && is_array($this->_data[$key])) {
                return new self($this->_data[$key]);
            } else {
                return $this->_data[$key];
            }
        } else {
            return $default;
        }
        //return (isset($this->_data[$key]) ? $this->_data[$key] : $default);
    }

    public function set($key, $value) {
        $this->_data[$key] = $value;
        $this->_count = count($this->_data);
    }

    public function has($key) {
        return isset($this->_data[$key]);
    }

    public function load($file, $section = '') {
        if (file_exists($file)) {
            $merge = array();
            $data = include($file);
            if($section) {
                $merge[$section] = $data;
            } else {
                $merge = $data;
            }
            $this->_data = $this->merge($this->_data, $merge);
            $this->_count = count($this->_data);
        } else {
            trigger_error('Error: Could not load config ' . $file . '!');
            exit();
        }
    }

    public function toArray() {
        $array = array();
        $data  = $this->_data;
        foreach ($data as $key => $value) {
            if ($value instanceof Config) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }
        return $array;
    }

    function merge($firstArray, $secondArray) {
        if (is_array($secondArray)) {
            foreach ($secondArray as $key => $val) {
                if (is_array($secondArray[$key])) {
                    $firstArray[$key] = (array_key_exists($key, $firstArray) && is_array($firstArray[$key])) ? $this->merge($firstArray[$key], $secondArray[$key]) : $secondArray[$key];
                } else {
                    $firstArray[$key] = $val;
                }
            }
        }
        return $firstArray;
    }

    /**
     * Defined by Countable interface
     *
     * @return int
     */
    public function count() {
        return $this->_count;
    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function current() {
        return current($this->_data);
    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function key() {
        return key($this->_data);
    }

    /**
     * Defined by Iterator interface
     *
     */
    public function next() {
        $this->_index++;
        return next($this->_data);
    }

    /**
     * Defined by Iterator interface
     *
     */
    public function rewind() {
        $this->_index = 0;
        return reset($this->_data);
    }

    /**
     * Defined by Iterator interface
     *
     * @return boolean
     */
    public function valid() {
        return $this->_index < $this->_count;
    }
    
    public function offsetSet($offset, $value) {
        if (is_array($value)) {
            $value = new self($value);
        }
        if ($offset === null) { // don't forget this! 
            $this->_data[] = $value; 
        } else { 
            $this->_data[$offset] = $value; 
        }
    }
    public function offsetExists($offset) {
        return isset($this->_data[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->_data[$offset]);
    }
    public function offsetGet($offset) {
        return $this->get($offset);
    }    

    public function __construct($array = array()) {
        $this->_index = 0;
        $this->_data = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->_data[$key] = new self($value);
            } else {
                $this->_data[$key] = $value;
            }
        }
        $this->_count = count($this->_data);
    }

    /**
     * Magic function so that $obj->value will work.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($key) {
        return $this->get($key);
    }

    /**
     * Only allow setting of a property if $allowModifications
     * was set to true on construction. Otherwise, throw an exception.
     *
     * @param  string $name
     * @param  mixed  $value
     * @throws Zend_Config_Exception
     * @return void
     */
    public function __set($key, $value) {
        return $this->set($key, $value);
    }

    /**
     * Support isset() overloading on PHP 5.1
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($key) {
        return isset($this->_data[$key]);
    }

    /**
     * Support unset() overloading on PHP 5.1
     *
     * @param  string $name
     * @throws Zend_Config_Exception
     * @return void
     */
    public function __unset($key) {
        unset($this->_data[$key]);
        $this->_count = count($this->_data);
    }

}

