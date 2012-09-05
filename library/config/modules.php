<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

return array(
    'error' => array(
        'name' => 'Error handle module',
        'description' => 'The error handle module',
        'controllers' => array(
            'error' => '*',
        ),
    ),
    'system' => array(
        'name' => 'System module',
        'description' => 'The system module',
        'controllers' => array(
            'index' => 'admin/%s',
            'admin' => 'admin/%s',
        ),
    ),
    'content' => array(
        'name' => 'Content',
        'description' => 'The content module',
        'controllers' => array(
            'front' => '*',
            'admin' => 'admin/%s',
        ),
    ),
    'user' => array(
        'name' => 'User',
        'description' => 'The user module', 
        'controllers' => array(
            'front' => '*',
            'admin' => 'admin/*'
        ),
    ),
);

