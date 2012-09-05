<?php

/**
 * Maxen Framework
 * 
 * @category    Maxen
 * @package     Config
 * @todo        Config Helper Class
 * @copyright   Copyright (c) 20010-2012 Maxen™ Ltd. (http://www.maxen.vn)
 * @license     none
 * @author      dino@maxen.vn
 * 
 */

class Config implements ArrayAccess {

    protected $data = array();

    /**
     * @todo Config Function : Load config from file
     */
    public function load($file, $section = '') {
        if (file_exists($file)) {
            $data = include($file);
            $this->loadArray($data, $section);
        }
    }

    /**
     * @todo Config Function : Load from array
     * @param array 	$array The array load to Config
     * @param string 	$section The string of section to load
     */
    public function loadArray($array, $section = '') {
        $data = array();
        if ($section) {
            $data[$section] = $array;
        } else {
            $data = $array;
        }
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->data[$key] = new self($value);
            } else {
                $this->data[$key] = $value;
            }
        }
    }

    /**
     * @todo Config Function : Merge two array to one
     */
    public function merge($firstArray, $secondArray) {
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
     * @todo Config Function : Convert data to array
     */
    public function toArray() {
        $array = array();
        $data = $this->data;
        foreach ($data as $key => $value) {
            if ($value instanceof Config) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }
        return $array;
    }

    /**
     * @todo Config Helper Function Section
     */
    public function get($key, $default = null) {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    public function set($key, $value) {
        if (is_array($value)) {
            $this->data[$key] = new self($value);
        } else {
            $this->data[$key] = $value;
        }
    }

    /**
     * @todo ArrayAccess Method Defined Section
     */
    public function offsetGet($key) {
        return $this->data[$key];
    }

    public function offsetSet($key, $value) {
        $this->set($key, $value);
    }

    public function offsetExists($key) {
        return isset($this->data[$key]);
    }

    public function offsetUnset($key) {
        unset($this->data[$key]);
    }

    /**
     * @todo Config Magic Funciton Section
     */
    public function __construct($data = array()) {
        $this->data = array();
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->data[$key] = new self($value);
            } else {
                $this->data[$key] = $value;
            }
        }
    }

    public function __get($key) {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function __set($key, $value) {
        $this->set($key, $value);
    }

    public function __unset($key) {
        unset($this->data[$key]);
    }

    public function __isset($key) {
        return isset($this->data[$key]);
    }

}

