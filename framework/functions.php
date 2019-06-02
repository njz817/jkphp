<?php
/**
 * 框架核心函数
 * User: Jack<376927050@qq.com>
 * Date: 2018/10/27
 * Time: 10:52
 */

if( !function_exists('config') ){
    /**
     * 获取配置项
     * @param $config_key
     * @param $def
     * @return mixed|string
     */
    function config($config_key,$def=''){
        return \framework\Config::getInstance()->get($config_key,$def);
    }
}

if( !function_exists('model') ){
    function model($model_name,$namespace = ''){
        if( empty($namespace) ){
            $module = \framework\Request::getInstance()->module();
            $namespace = "\application\\$module\model\\";
        }
        $model_name = $namespace . $model_name;
        return new $model_name();
    }
}