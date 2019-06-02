<?php
/**
 * 配置信息类
 * User: Jack<376927050@qq.com>
 * Date: 2019/05/24
 * Time: 17:34
 */

namespace framework;

class Config
{
    private static $instance;
    private static $arrConfig = [];
    private function __construct(){}

    public static function getInstance()
    {
        if( self::$instance == null ){
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 加载项目中的配置文件
     * @param $configFile
     * @throws FileNotFoundException
     */
    public function load($configFile)
    {
        if( !file_exists($configFile) ){
            throw new FileNotFoundException();
        }
        $config = require_once $configFile;
        self::$arrConfig = array_merge(self::$arrConfig,$config);
    }

    /**
     * 获取配置项  $key  db.dbhost
     * @param $key
     * @param string $default
     */
    public function get($key,string $default = '')
    {
        if( strpos($key,'.') !== false ){
            $arrTemp = explode('.',$key);
            $index1 = $arrTemp[0];
            $index2 = $arrTemp[1];
            if( isset(self::$arrConfig[$index1][$index2]) ){
                return self::$arrConfig[$index1][$index2];
            }
        }
        if( key_exists($key,self::$arrConfig) ){
            return self::$arrConfig[$key];
        }
        return $default;
    }

    /**
     * 动态修改配置信息
     * @param $key
     * @param $value
     */
    public function set(string $key,string $value)
    {
        self::$arrConfig[$key] = $value;
    }
}