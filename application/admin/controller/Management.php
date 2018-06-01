<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/4
 * Time: 14:41
 */
namespace app\admin\controller;
use base\Adminbase;
use think\Db;
use ku\Verify;
class  Management extends Adminbase{

    public function index(){
        $name = $this->request->param('name','','string');
        $this->assign($name,$name);
        $where = [];
        if(!empty($name))
            $where['name'] = $name;
        $admins = Db::name('admin')->where($where)->paginate(10,false)->toArray();
        $this->assign('pager',$admins);
        return $this->fetch('index');
    }


    public function addAdmin(){
        $name = $this->request->param('name','','string');
        $mobile = $this->request->param('mobile','','string');
        if(!Verify::isMobile($mobile))
            return $this->returnJson('手机格式不正确!');
        if(empty($name))
            return $this->returnJson('用户名不为空!');
        $password = $this->request->param('name','','string');
        if(strlen($password)<6 or strlen($password)>30)
            return $this->returnJson('密码长度在6-30之间!');
        $exist = Db::name('admin')->where('name',$name)->whereOr('mobile',$mobile)->find();
        if(!empty($exist))
            return $this->returnJson('用户名或手机已注册管理员!');
        $add = ['name'=>$name,'mobile'=>$mobile,'create_time'=>time()];
        $password = sha1($password.$name);
        $add['password'] = $password;
        $res = Db::name('admin')->insert($add);
        if(!$res)
            return $this->returnJson('失败！，请重试！');
        return $this->returnJson('成功',true,1);

    }

}