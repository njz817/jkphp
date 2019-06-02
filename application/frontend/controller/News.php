<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/25 0025
 * Time: 上午 10:05
 */

namespace application\frontend\controller;

use framework\Controller;
use framework\libs\Captcha;
use framework\Request;

class News extends Controller
{
    public function index()
    {
    }

    public function show()
    {
        $aid = Request::getInstance()->get('aid');
        $model = model('News');
        $data = $model->find($aid);
        return $this->render('show',['news'=>$data]);
    }
}