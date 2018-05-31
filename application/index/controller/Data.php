<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/4/10
 * Time: 10:45
 */
namespace app\index\controller;
use think\Db;
use base\Base;

class Data extends Base{

    public function userName(){
        $id = $this->request->param('id',0,'int');
        $name = '';
        $user = Db::name('user')->where('Id',$id)->find();
        if(!empty($user))
            $name = $user['name'];
        return $this->returnJson('','true',1,['name'=>$name]);
    }


}