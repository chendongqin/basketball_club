<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/2/28
 * Time: 20:39
 */
namespace app\index\controller;
use think\Controller;
use ku\Verify;
use think\Cache;
use think\Db;
use ku\Email as kuEmail;

class Email extends Controller{

    public function index(){

    }

    //注册邮箱验证码发送
    public function regist(){
        $request = $this->request;
        $email = $request->param('email','','string');
        if(!Verify::isEmail($email)){
            return json(array('status'=>false,'msg'=>'邮箱格式不正确'));
        }
        $code = mt_rand(100000,999999);
        $cacheRes = Cache::set('regist_'.$email,$code,200);
        if(!$cacheRes){
            return json(array('status'=>false,'msg'=>'验证码存储错误'));
        }
        $subject = '来战吧篮球注册';
        $body = '验证码：'.$code;
        $res = kuEmail::sendEmail($email,$subject,$body);
        if($res){
            return json(array('status'=>true,'msg'=>'发送成功'));
        }else{
            return json(array('status'=>false,'msg'=>'发送失败'));
        }
    }

    public function findback(){
        $email = $this->request->param('email','','string');
        if(!Verify::isEmail($email)){
            return json(array('status'=>false,'msg'=>'邮箱格式不正确'));
        }
        $user = Db::name('students')->where(array('email'=>$email))->find();
        if(empty($user)){
            $user = Db::name('teachers')->where(array('email'=>$email))->find();
            if(empty($user)){
                return json(array('status'=>false,'msg'=>'该邮箱未注册'));
            }
        }
        $code = mt_rand(100000,999999);
        $cacheRes = Cache::set('findback_'.trim($email),$code,200);
        if(!$cacheRes){
            return json(array('status'=>false,'msg'=>'验证码存储错误'));
        }
        $subject = '用户密码找回';
        $body = '验证码：'.$code;
        $res = kuEmail::sendEmail($email,$subject,$body);
        if($res){
            return json(array('status'=>true,'msg'=>'发送成功'));
        }else{
            return json(array('status'=>false,'msg'=>'验证发送失败'));
        }
    }


}
