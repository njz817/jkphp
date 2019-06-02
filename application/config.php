<?php
/**
 * 我们需要一个对象 来读取这些配置，并且能很多地方 获取配置
 * User: Jack<376927050@qq.com>
 * Date: 2018/10/24
 * Time: 17:27
 */
return [
    'database'=> [
        'host'=> 'localhost',
        'dbname'=> 'mydb',
        'user'=> 'root',
        'password'=> 'root',
        'charset'=> 'utf8',
    ],
    'default_module' => 'frontend',
    'default_controller' => 'News',
    'default_action' => 'index'
];