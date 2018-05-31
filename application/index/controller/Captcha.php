<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/4
 * Time: 14:48
 */
namespace app\index\controller;
use think\Controller;
use ku\Tool;
class Captcha extends Controller{

    public function index(){
        $request = $this->request;
        $channel = $request->param('channel','','string');
        Tool::captcha(4,100,40,$channel);
    }

}