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

class Club extends Userbase{

    public function index(){
        $id= $this->request->param('id','','int');
        $user =$this->getUser();
        $myClub = Db::name('club')->where('Id',$id)->find();
        if(empty($myClub))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'球队不存在']);
        $players = json_decode($myClub['players'],true);
        if(!isset($players[$user['Id']]))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'您未加入该球队']);
        $this->assign('club',$myClub);
        $this->assign('players',$players);
        return $this->fetch();
    }

    public  function player(){
        $Id= $this->request->param('id','','int');
        $user = Db::name('user')->where('Id',$Id)->find();
        if(empty($user))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'没有改球员']);
        $this->assign('user',$user);
        return $this->fetch();
    }

}