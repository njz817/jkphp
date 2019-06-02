<?php
/**
 * Created by PhpStorm.
 * User: Jack<376927050@qq.com>
 * Date: 2018/10/27
 * Time: 14:17
 */
namespace framework\helper;

use framework\Request;

class Url
{
    public static function to($path,array $params = [])
    {
        $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
        $newArray = array_combine(['p','c','a'],self::_handleParams($path));
        if( !empty($params) ){
            $newArray = array_merge($newArray,$params);
        }
        return $url . '?' .http_build_query($newArray);
    }

    /**
     * 完善参数
     * @param $params
     */
    private static function _handleParams($path)
    {
        $arrPath = explode('/',$path);
        switch (count($arrPath))
        {
            case 1:
                array_unshift($arrPath,Request::getInstance()->module,Request::getInstance()->controller);
                break;
            case 2:
                array_unshift($arrPath,Request::getInstance()->module);
                break;
        }
        return $arrPath;
    }
}