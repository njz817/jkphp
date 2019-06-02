<?php
/**
 * 请求对象
 * User: Jack<376927050@qq.com>
 * Date: 2018/10/27
 * Time: 11:21
 */

namespace framework;


class Request
{
    // 模块
    protected $module;
    // 控制器
    protected $controller;
    // 方法
    protected $action;

    private static $_instance;

    /**
     * 初始化 处理请求参数
     * Request constructor.
     */
    private function __construct()
    {
        $module = $_GET['m'] ?? config('default_module');
        $this->module = strtolower(trim($module));

        $controller = $_GET['c'] ?? config('default_controller');
        $this->controller = ucfirst(strtolower(trim($controller)));

        $action = $_GET['a'] ?? config('default_action');
        $this->action = strtolower(trim($action));
    }

    /**
     * 获取对象实例
     * @return Request
     */
    public static function getInstance()
    {
        if( self::$_instance == null ){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 获取请求的模块名
     * @return string
     */
    public function module()
    {
        return $this->module;
    }

    /**
     * 获取请求的控制器名
     * @return string
     */
    public function controller()
    {
        return '\application\\' . $this->module . '\controller\\' . $this->controller;
    }

    /**
     * 获取不带命名空间的控制器名
     * @return string
     */
    public function contr()
    {
        return $this->controller;
    }

    /**
     * 获取请求的方法名
     * @return string
     */
    public function action()
    {
        return $this->action;
    }

    /**
     * 获取请求方法
     * @return string
     */
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 获取客户端请求ip地址
     * @return mixed
     */
    public function getIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * 判断是否是ajax
     * @return bool
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ? true : false;
    }

    /**
     * 判断是不是get请求
     * @return bool
     */
    public function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
    }

    /**
     * 判断是不是post请求
     * @return bool
     */
    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST' ? true : false;
    }

    /**
     * 获取post数据
     * @param string $key
     * @return mixed
     */
    public function post($key = '',$safety = ['trim','htmlspecialchars'])
    {
        if(!empty($_POST) && !empty($safety) ){
            foreach ($safety as $func){
                $_POST = array_map($func,$_POST);
            }
        }

        if( !empty($key)  ){
            return isset($_POST[$key]) ? trim($_POST[$key]) : null;
        }
        return $_POST;
    }

    /**
     * 获取post数据
     * @param string $key
     * @return mixed
     */
    public function get($key = '',$safety = ['trim','htmlspecialchars'])
    {
        if(!empty($_GET) && !empty($safety) ){
            foreach ($safety as $func){
                $_GET = array_map($func,$_GET);
            }
        }
        if( !empty($key)  ){
            return isset($_GET[$key]) ? trim($_GET[$key]) : null;
        }
        return $_GET;
    }

    /**
     * 获取不存在的属性
     * @param $name
     */
    public function __get($name)
    {
        if( method_exists($this,$name) ){
            return $this->$name();
        }
    }

    // 私有化克隆
    private function __clone(){}
}