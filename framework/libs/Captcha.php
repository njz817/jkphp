<?php
/**
 * 验证码类
 * User: Jack<376927050@qq.com>
 * Date: 2018/10/27
 * Time: 15:18
 */
namespace framework\libs;

class Captcha
{
    // 验证码宽度
    protected static $yzmWidth = 250;
    // 验证码高度
    protected static $yzmHeight = 62;
    // 字符集合
    protected static $codeSet = '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXYZ';
    // 字符个数
    protected static $length = 5;
    // 是否加混淆曲线
    protected static $useCurve = true;
    // 是否加干扰点
    protected static $useDot = true;
    // 字符尺寸
    protected static $fontSize = 30;

    // 存储标识
    private static $_key = 'captcha';


    /**
     * 显示验证码
     * @param array $config
     */
    public static function show(array $config = [])
    {
        if( !empty($config) )
            self::assignment($config);
        //创建图片资源
        $img = imagecreate(self::$yzmWidth,self::$yzmHeight);
        //随机生成背景颜色
        $bg = imagecolorallocate($img,rand(50,200),rand(0,155),rand(0,155));
        //设置字体颜色和样式
        $fontColor = imagecolorallocate($img,255,255,255);
        $fontstyle = LIBRARY_PATH . DS .'font'.DS.'captcha.ttf';

        $string = self::getCodeString();
        //生成指定长度的验证码
        for($i=0; $i<self::$length; $i++ ){
            imagettftext($img,
                self::$fontSize, 						//字符尺寸
                rand(0,20) - rand(0,25),  //随机设置字符倾斜角度
                32 + $i*40, rand(30,50),  //随机设置字符坐标
                $fontColor, 				//字符颜色
                $fontstyle, 				//字符样式
                $string{$i} 				//字符内容
            );
        }
       if( self::$useCurve ){
           //添加8个干扰线
           for($i=0; $i<8; ++$i){
               //随机生成干扰线颜色
               $lineColor = imagecolorallocate($img,rand(0,255),rand(0,255),rand(0,255));
               //随机生成干扰线
               imageline($img,rand(0,self::$yzmWidth),0,rand(0,self::$yzmWidth),self::$yzmHeight,$lineColor);
           }
       }

       if(self::$useDot){
           //添加250个噪点
           for($i=0; $i<250; ++$i) {
               //随机生成噪点位置
               imagesetpixel($img,rand(0,self::$yzmWidth),rand(0,self::$yzmHeight),$fontColor);
           }
       }

        //设置发送的信息头内容
        header('Content-type:image/png');
        //输出图片
        imagepng($img);
        //释放图片所占内存
        imagedestroy($img);
    }

    /**
     * 校验验证码
     * @param $input
     * @return bool
     */
    public static function verify($input)
    {
        $captcha = isset($_SESSION[self::$_key]) ? $_SESSION[self::$_key] : null;
        if(!empty($captcha)){
            //清除验证码，防止重复验证
            unset($_SESSION[self::$_key]);
            //不区分大小写
            return strtoupper($captcha) == strtoupper($input);
        }
        return false;
    }

    /**
     * 用户传参赋值
     * @param array $params
     */
    protected static function assignment(array $params)
    {
        foreach ($params as $property=>$value){
            if( property_exists(__CLASS__,$property) ){
                self::$$property = $value;
            }
        }
    }

    /**
     * 生成随机字符
     * @return bool|string
     */
    protected static function getCodeString()
    {
        $code = substr(str_shuffle(self::$codeSet),0,self::$length);
        isset($_SESSION) || session_start();
        $_SESSION[self::$_key] = $code;
        return $code;
    }
}