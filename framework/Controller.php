<?php
/**
 * 控制器基类
 * User: Jack<376927050@qq.com>
 * Date: 2018/10/24
 * Time: 14:12
 */

namespace framework;

class Controller
{
    private $_view;

    public function __construct()
    {
        $this->_view = new View();
    }

    public function __call($name, $arguments)
    {
        if( is_callable([$this->_view,$name]) ){
            call_user_func_array([$this->_view,$name],$arguments);
        }
    }
}