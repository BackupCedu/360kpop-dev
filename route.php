<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

return array(
    // Route : Homepage
    'index' => array(
        '', 
        array(
            'module' => 'content',
            'controller' => 'index',
            'action' => 'index',
        ),
        array(),
    ),
    // Route : Category
    'category' => array(
        'category/(\w+)/(\d+)', 
        array(
            'module' => 'content',
            'controller' => 'index',
            'action' => 'cate',
        ),
        array(
            '1' => 'alias',
            '2' => 'cid',
        ),
    ),
    // Route : Post
    'article' => array(
        'article/(\w+)/(\d+)', 
        array(
            'module' => 'content',
            'controller' => 'index',
            'action' => 'article',
        ),
        array(
            '1' => 'alias',
            '2' => 'pid',
        ),
    ),
    // Route : admin
    'admin' => array(
        'admin/(\w+)/(\w+)', 
        array(
            'module' => 'admin',
        ),
        array(
            '1' => 'controller',
            '2' => 'action',
        ),
    ),
    // Route : user
    'user' => array(
        'user/(\w+)', 
        array(
            'module' => 'user',
            'controller' => 'index',
        ),
        array(
            '1' => 'action',
        ),
    ),
);

?>
