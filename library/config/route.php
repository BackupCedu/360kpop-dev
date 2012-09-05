<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

return array(
    /**
     * @todo User route section
     */
    'login' => array(
        'module' => 'user',
        'controller' => 'index',
        'action' => 'login',
    ),
    'logout' => array(
        'module' => 'user',
        'controller' => 'index',
        'action' => 'logout',
    ),
    'user/(\d+)' => array(
        'type' => 'regex',
        'module' => 'user',
        'controller' => 'index',
        'action' => 'detail',
        'map' => array(
            '1' => 'uid',
        ),
    ),
    'profile/(\w+)\.(\d+)' => array(
        'type' => 'regex',
        'module' => 'user',
        'controller' => 'index',
        'action' => 'profile',
        'map' => array(
            '1' => 'alias',
            '2' => 'uid',
        ),
    ),
    /**
     * @todo Admin control panel section
     */
    'admin/:controller/:action' => array(
        'module' => 'system',
        'controller' => 'admin',
    ),
    'admin/:controller/:action' => array(
        'module' => 'system',
        'controller' => 'admin',
    ),
    /**
     * @todo Content route section
     */
    'category/:cid' => array(
        'module' => 'content',
        'controller' => 'index',
        'action' => 'cate',
    ),
    '([\w\-]+)\-c(\d+)' => array(
        'type' => 'regex',
        'module' => 'content',
        'controller' => 'index',
        'action' => 'cate',
        'map' => array(
            '1' => 'alias',
            '2' => 'cid',
        ),
    ),
    'node/:nid' => array(
        'module' => 'content',
        'controller' => 'index',
        'action' => 'node',
    ),
    '[\w\-]+\-c(\d+)/([\w\-]+)\-n(\d+).html' => array(
        'type' => 'regex',
        'module' => 'content',
        'controller' => 'index',
        'action' => 'node',
        'map' => array(
            '1' => 'cid',
            '2' => 'alias',
            '3' => 'nid',
        ),
    ),
);

