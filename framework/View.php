<?php
/**
 * Created by PhpStorm.
 * User: Jack<376927050@qq.com>
 * Date: 2018/10/27
 * Time: 11:55
 */

namespace framework;


use framework\helper\Url;

class View
{
    // 视图路径
    protected $view;

    protected $file_extension = '.php';

    public function __construct()
    {
        $module = Request::getInstance()->module();
        $this->view = implode(DS,[APP_PATH,$module,'view']);
    }

    /**
     * 渲染视图
     * @param $template
     * @param array $data
     */
    public function render(string $template = '',array $data = [])
    {
        $view_file = !empty($template) ? $template : Request::getInstance()->action();
        extract($data);
        $controller = strtolower(Request::getInstance()->contr());
        require_once $this->view . DS .$controller. DS . $view_file . $this->file_extension;
    }

    /**
     * 警告弹窗跳转
     * @param string $message
     * @param string $url
     */
    public function renderJs($message = '',$url = '',$params = [])
    {
        $realUrl = Url::to($url,$params);
        echo "<script>alert('$message');location.href='$realUrl';</script>";
    }

    /**
     * 设置跳转方法
     * @param $url
     * @param array $params
     */
    public function redirect($url,array $params=[])
    {
        $realUrl = Url::to($url,$params);
        header("location:$realUrl");
    }

}