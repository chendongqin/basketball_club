<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/4
 * Time: 15:41
 */
namespace app\index\controller;
use think\Session;
use base\Base;
use think\Db;
 class Index extends Base{

     public function index(){
         $this->assign('title','首页');
         return $this->fetch();
     }

     //赛事
     public function event(){
         $eventModel = Db::name('event');
         $where = array(
             'audit'=>1,
//             'start_time'=>array('<=',date('Y-m-d')),
             'status'=>array('<>',2),
         );
         $request = $this->request;
         $name = $request->param('name','','string');
         if(!empty($name)){
             $where['name'] = array('like','%'.$name.'%');
         }
         $page = $request->param('page',1,'int');
         $events = $eventModel->where($where)->paginate(4,false,array('page'=>$page))->toArray();
         $this->assign('events',$events);
         $this->assign('title','来战吧篮球');
         return $this->fetch();
     }

     public function clubs(){
         $where= [];
         $page = $this->request->param('page',1,'int');
         $name = $this->request->param('name','','string');
         if(!empty($name))
             $where['name'] = ['like','%'.$name.'%'];
         $area = $this->request->param('area','','string');
         if(!empty($name))
             $where['area'] = ['like','%'.$area.'%'];
         $clubs  = Db::name('club')
             ->where($where)
             ->order('create_time')
             ->paginate(10,false,['page'=>$page])
             ->toArray();
         $this->assign('clubs',$clubs);
         $provinces = Db::name('provinces')->select();
         $proData = array();
         foreach ($provinces as $item){
             $proData[$item['provinceid']] = $item['province'];
         }
         $this->assign('provinces',$proData);
         return $this->fetch();
     }

 }