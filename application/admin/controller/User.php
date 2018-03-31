<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/13
 * Time: 10:50
 */
namespace  app\admin\controller;
use base\Adminbase;
use think\Db;
use think\Config;

class User extends Adminbase{


    public function index(){

    }

    public function data(){
        $userId = $this->request->param('userId',0,'int');
        if(empty($userId)){
           return $this->returnJson('用户不存在');
        }
        $user = Db::name('user')->where(array('Id'=>$userId))->find();
        if(empty($user)){
            return $this->returnJson('用户不存在');
        }
        unset($user['password']);
        $user['ban'] == 0?'正常用户':'禁用用户';
        return $this->returnJson('获取成功',true,1,$user);
    }

}