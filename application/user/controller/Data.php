<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/4/1
 * Time: 10:51
 */
namespace app\user\controller;
use base\Userbase;
use think\Db;

class Data extends Userbase{

    public function index(){

    }

    //获取用户数据
    public function user(){
        $userId = $this->request->param('id','','int');
        $user = Db::name('user')->where('Id',$userId)->find();
        if(empty($user))
            return $this->returnJson('用户不存在');
        return $this->returnJson('获取成功',true,1,$user);
    }

}