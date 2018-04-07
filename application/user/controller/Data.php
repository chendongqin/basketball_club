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

    //通过姓名获取球队
    public function clubsOfname(){
        $name = $this->request->param('name','','string');
        if(empty($name))
            $clubs = [];
        else
            $clubs = Db::name('club')->where('name','like','%'.$name);
        return $this->returnJson('获取成功',true,1,$clubs);
    }

    //通过姓名获取球队
    public function userOfname(){
        $name = $this->request->param('name','','string');
        if(empty($name))
            $users = [];
        else
            $users = Db::name('user')->where('name','like','%'.$name);
        return $this->returnJson('获取成功',true,1,$users);
    }

}