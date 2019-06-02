<?php
namespace framework;

use framework\helper\Helper;

class Error
{
    public static function register()
    {
        set_exception_handler([__CLASS__,'appException']);
        set_error_handler([__CLASS__,'appError']);
    }

    /**
     * 异常处理
     * @param \Exception $e
     */
    public static function appException(\Throwable $e)
    {
        if( $e instanceof \Error){
            var_dump($e);
        }elseif($e instanceof \Exception){
            var_dump($e);
        }
    }

    /**
     * 错误处理 非致命报错
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     */
    public static function appError($errno,$errstr,$errfile,$errline)
    {
        $error = Helper::json([
                'errno'=>$errno,
                'errstr'=>$errstr,
                'errfile'=>$errfile,
                'errline'=>$errline
        ]);
        throw new \framework\exception\ErrorException($error);
    }
}