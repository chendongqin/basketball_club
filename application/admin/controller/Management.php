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

}