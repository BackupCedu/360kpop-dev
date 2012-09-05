<?php

/**
 * @package    Maxen Framework
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

abstract class Factory {

    /**
     * @var    Application
     * @since  11.1
     */
    public static $application = null;

    /**
     * @var    Cache
     * @since  11.1
     */
    public static $cache = null;

    /**
     * @var    Config
     * @since  11.1
     */
    public static $config = null;
    
    /**
     * @var    Registry
     * @since  11.1
     */
    public static $registry = null;
    
    /**
     * @var    Router
     * @since  11.1
     */
    public static $router = null;

    /**
     * @var    array
     * @since  11.3
     */
    public static $dates = array();

    /**
     * @var    Session
     * @since  11.1
     */
    public static $session = null;

    /**
     * @var    Language
     * @since  11.1
     */
    public static $language = null;

    /**
     * @var    Document
     * @since  11.1
     */
    public static $document = null;

    /**
     * @var    User
     * @since  11.1
     */
    public static $user = null;

    /**
     * @var    Database
     * @since  11.1
     */
    public static $database = null;

    /**
     * @var    Mail
     * @since  11.1
     */
    public static $mailer = null;
    
    /**
     * @var    root dir
     * @since  11.1
     */
    public static $rootDir = '';
    
    /**
     * @todo Set application root directory
     */
    public static function getRoot($dir = null) {
        if(!self::$rootDir && $dir) {
            self::$rootDir = $dir;
        }
        
        return self::$rootDir;
    }
    
    public static function getLibrary() {
        return self::getRoot() . '/library';
    }

    /**
     * Get a application object.
     *
     * Returns the global {@link Application} object, only creating it if it doesn't already exist.
     *
     * @param   mixed   $id      A client identifier or name.
     * @param   array   $config  An optional associative array of configuration settings.
     * @param   string  $prefix  Application prefix
     *
     * @return  Application object
     *
     * @see     Application
     * @since   11.1
     */
    public static function getApplication($config = array()) {
        if (!self::$application) {
            self::$application = Application::getInstance($config);
        }

        return self::$application;
    }

    /**
     * Get a configuration object
     *
     * Returns the global {@link JRegistry} object, only creating it if it doesn't already exist.
     *
     * @param   string  $file  The path to the configuration file
     * @param   string  $type  The type of the configuration file
     *
     * @return  JRegistry
     *
     * @see     JRegistry
     * @since   11.1
     */
    public static function getConfig() {
        if (!self::$config) {
            $dir = self::getRoot() . '/library/config';

            self::$config = new Config();
            self::$config->load($dir . '/default.php');
            self::$config->load($dir . '/modules.php', 'modules');
            self::$config->load($dir . '/route.php', 'routes');
        }

        return self::$config;
    }
    
    public static function getRegistry() {
        if (!self::$registry) {
            self::$registry = new Registry();
        }

        return self::$registry;
    }
    
    public static function getRouter() {
        if (!self::$router) {
            self::$router = new Router();
        }

        return self::$router;
    }

    /**
     * Get a session object.
     *
     * Returns the global {@link JSession} object, only creating it if it doesn't already exist.
     *
     * @param   array  $options  An array containing session options
     *
     * @return  JSession object
     *
     * @see     JSession
     * @since   11.1
     */
    public static function getSession($options = array()) {
        if (!self::$session) {
            self::$session = self::createSession($options);
        }

        return self::$session;
    }

    /**
     * Get a language object.
     *
     * Returns the global {@link JLanguage} object, only creating it if it doesn't already exist.
     *
     * @return  JLanguage object
     *
     * @see     JLanguage
     * @since   11.1
     */
    public static function getLanguage() {
        if (!self::$language) {
            self::$language = self::createLanguage();
        }

        return self::$language;
    }

    /**
     * Get a document object.
     *
     * Returns the global {@link JDocument} object, only creating it if it doesn't already exist.
     *
     * @return  JDocument object
     *
     * @see     JDocument
     * @since   11.1
     */
    public static function getDocument() {
        if (!self::$document) {
            self::$document = self::createDocument();
        }

        return self::$document;
    }

    /**
     * Get an user object.
     *
     * Returns the global {@link JUser} object, only creating it if it doesn't already exist.
     *
     * @param   integer  $id  The user to load - Can be an integer or string - If string, it is converted to ID automatically.
     *
     * @return  JUser object
     *
     * @see     JUser
     * @since   11.1
     */
    public static function getUser($id = null) {
        if (is_null($id)) {
            $instance = self::getSession()->get('user');
            if (!($instance instanceof JUser)) {
                $instance = JUser::getInstance();
            }
        } else {
            $current = self::getSession()->get('user');
            if ($current->id != $id) {
                $instance = JUser::getInstance($id);
            } else {
                $instance = self::getSession()->get('user');
            }
        }

        return $instance;
    }

    /**
     * Get a cache object
     *
     * Returns the global {@link JCache} object
     *
     * @param   string  $group    The cache group name
     * @param   string  $handler  The handler to use
     * @param   string  $storage  The storage method
     *
     * @return  JCache object
     *
     * @see     JCache
     */
    public static function getCache($group = '', $handler = 'callback', $storage = null) {
        $hash = md5($group . $handler . $storage);
        if (isset(self::$cache[$hash])) {
            return self::$cache[$hash];
        }
        $handler = ($handler == 'function') ? 'callback' : $handler;

        $options = array('defaultgroup' => $group);

        if (isset($storage)) {
            $options['storage'] = $storage;
        }

        $cache = JCache::getInstance($handler, $options);

        self::$cache[$hash] = $cache;

        return self::$cache[$hash];
    }

    /**
     * Get an authorization object
     *
     * Returns the global {@link JACL} object, only creating it
     * if it doesn't already exist.
     *
     * @return  JACL object
     */
    public static function getACL() {
        if (!self::$acl) {
            self::$acl = new JAccess;
        }

        return self::$acl;
    }

    /**
     * Get a database object.
     *
     * Returns the global {@link JDatabase} object, only creating it if it doesn't already exist.
     *
     * @return  JDatabase object
     *
     * @see     JDatabase
     * @since   11.1
     */
    public static function getDbo() {
        if (!self::$database) {
            //get the debug configuration setting
            $conf = self::getConfig();
            $debug = $conf->get('debug');

            self::$database = self::createDbo();
            self::$database->setDebug($debug);
        }

        return self::$database;
    }

    /**
     * Get a mailer object.
     *
     * Returns the global {@link JMail} object, only creating it if it doesn't already exist.
     *
     * @return  JMail object
     *
     * @see     JMail
     * @since   11.1
     */
    public static function getMailer() {
        if (!self::$mailer) {
            self::$mailer = self::createMailer();
        }
        $copy = clone self::$mailer;

        return $copy;
    }
}
