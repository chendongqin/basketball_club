<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/4
 * Time: 15:22
 */
namespace app\user\controller;
use base\Base;
use think\Session;
class Logout extends Base{

    public function index(){
        $sission = new Session();
        $sission->delete('user');
        $this->redirect($this->request->server('HTTP_REFERER'));
    }

}