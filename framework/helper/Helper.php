<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/24 0024
 * Time: 下午 5:02
 */

namespace framework\helper;


class Helper
{
    /**
     * json格式转换
     * @param array $data
     * @param string $type
     * @return string
     */
    public static function json(array $data,int $type = JSON_UNESCAPED_UNICODE)
    {
        return json_encode($data,$type);
    }
}