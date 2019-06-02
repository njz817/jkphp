<?php
namespace framework;

class Loading
{
    public static function start()
    {
        # 框架初始化配置
        self::init();
        # 请求分发
        self::dispatch();
    }

    /**
     * 请求分发
     * @throws \Exception
     */
    public static function dispatch()
    {
        $request = Request::getInstance();
        list($controller,$method) = [$request->controller(),$request->action()];
        $objCtrl = new $controller();
        if( !is_callable([$objCtrl,$method]) ){
            throw new BadRequestException();
        }
        call_user_func([$objCtrl,$method]);
    }

    /**
     * 框架初始化
     */
    public static function init()
    {
        # 定义时区
        date_default_timezone_set('PRC');

        # 定义常用路径常量
        define('DS',DIRECTORY_SEPARATOR);
        define('ROOT',dirname(__DIR__));
        define('APP_PATH', ROOT . DS . 'application');
        define('FRAMEWORK_PATH', ROOT . DS . 'framework');
        define('LIBRARY_PATH',FRAMEWORK_PATH . DS . 'libs');

        # 自动加载
        spl_autoload_register([__CLASS__,'autoload']);

        # 定义错误处理、异常处理
        Error::register();

        # 读取应用里的配置文件
        $confFile = APP_PATH . DS . 'config.php';
        Config::getInstance()->load($confFile);

        # 引用核心公共函数
        require_once FRAMEWORK_PATH .DS .'functions.php';

        # 开启session
        session_start();
    }

    /**
     * 定义自动加载
     * @param $classname
     * @throws \Exception
     */
    protected static function autoload($classname)
    {
        // 兼容Unix操作系统
        $classfile = ROOT . DS . str_replace('\\','/',$classname) . '.php';
		$classfile = str_replace('app','application',$classfile);
        if( !file_exists($classfile) ){
            throw new \framework\exception\FileNotFoundException($classfile.'文件不存在');
        }
        require_once $classfile;
    }
}