<?php
//图片生成缩略图
class Image{
	//图片类型和对应创建画布资源的函数名
	private static $from = array(
		'image/png'=>'imagecreatefrompng',
		'image/jpeg'=>'imagecreatefromjpeg'
	);
	//图片类型和对应生成图片的函数名
	private static $to = array(
		'image/png'  => 'imagepng',
		'image/jpeg' => 'imagejpeg'
	);
	private $file;   //图像路径
	private $image;  //图像资源
	private $width;  //原图宽度
	private $height; //原图高度
	private $mime;   //原图类型
	private $thumb;  //缩略图资源
	/**
	 * 构造方法
	 * @param string $file 图片路径
	 */
	public function __construct($file) {
		//检查传入的文件是否合法
		if(!self::_checkFile($file)){
			throw new Exception('缩略图初始化失败：传入文件不合法。');
		}
		$this->file = $file;
		//获取图片信息
		$info = getimagesize($this->file);
		$this->width = $info[0];      //图片宽度
		$this->height = $info[1];     //图片高度
		$this->mime = $info['mime'];  //图片类型
		//使用可变方法创建原图资源
		$func = self::$from[$this->mime];
		$this->image = $func($this->file);
	}
	/**
	 * 生成缩略图（等比例填充白色背景）
	 * @param int $width 目标宽度
	 * @param int $height 目标高度
	 * @return $this 返回对象本身以供连贯操作
	 */
	public function thumbFilled($width, $height){
		//等比例缩放计算
		if($this->width/$width > $this->height/$height) {
			$dst_width = $width;
			$dst_height = round($width / $this->width * $this->height);
		}else{
			$dst_height = $height;
			$dst_width = round($height / $this->height * $this->width);
		}
		//创建缩略图资源
		$this->thumb = imagecreatetruecolor($width, $height);
		//填充白色背景
		imagefill($this->thumb, 0, 0, imagecolorallocate($this->thumb, 255, 255, 255));
		//计算缩略图在画布上的位置
		$dst_x = ($width-$dst_width)/2;
		$dst_y = ($height-$dst_height)/2;
		//将按比例将缩略图重新采样，调整其位置
		imagecopyresampled($this->thumb, $this->image, $dst_x, $dst_y, 0, 0, $dst_width, $dst_height, $this->width, $this->height);
		//返回对象本身
		return $this;
	}
	/**
	 * 生成缩略图（等比例缩放）
	 * @param int $width  最大宽度
	 * @param int $height 最大高度
	 * @param bool $fill  是否最大缩放
	 * @return $this 返回对象本身以供连贯操作
	 */
	public function thumbScale($width, $height, $fill=false){
		$dst_width = $this->width;
		$dst_height = $this->height;
		//等比例缩放计算
		if($this->width/$width > $this->height/$height){
			if($fill || ($this->width > $width)){
				$dst_width = $width;
				$dst_height = round($dst_width / $this->width * $this->height);
			}
		}else{
			if($fill || ($this->height > $height)){
				$dst_height = $height;
				$dst_width = round($dst_height / $this->height * $this->width);	
			}
		}
		//创建缩略图资源
		$this->thumb = imagecreatetruecolor($dst_width, $dst_height);
		//将原图缩放填充到缩略图画布中
		imagecopyresized($this->thumb, $this->image, 0, 0, 0, 0, $dst_width, $dst_height, $this->width, $this->height);
		//返回对象本身
		return $this;
	}
	/**
	 * 保存缩略图到文件
	 * @param string $savePath 保存路径d
	 */
	public function save($savePath){
		//准备目录
		$path = dirname($savePath);
		is_dir($path) || mkdir($path,0777,true);
		//生成图片并保存到文件
		$func = self::$to[$this->mime];
		$func($this->thumb, $savePath);
	}
	/**
	 * 过滤危险字符、判断文件是否存在、是否为支持的格式
	 * @param string $file 图片路径
	 * @return bool 判断结果
	 */
	private static function _checkFile($file){
		//去除字符串前后的空白、斜线
		$file = is_string($file) ? trim(trim($file),'/') : '';
		//验证通过返回 true，失败返回 false
		return (
			//只允许字母、数字、下划线、斜线、横线、点，长度在1~255之间
			(preg_match('/^[\w\/\.\-]{1,255}$/', $file))
			//路径中不允许出现“..”
			&& (false === strpos($file,'..'))
			//验证文件扩展名
			&& (in_array(strrchr($file, '.'),array('.jpg','.png')))
			//判断文件是否真实存在
			&& (is_file($file))
		);
	}
}