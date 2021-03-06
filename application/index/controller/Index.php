<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/4
 * Time: 15:41
 */
namespace app\index\controller;
use think\Config;
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
             'status'=>array('<>',2),
         );
         $request = $this->request;
         $name = $request->param('name','','string');
         if(!empty($name)){
             $where['name'] = array('like','%'.$name.'%');
         }
         $type = $request->param('type','-1','int');
         if($type!=-1)
             $where['type'] = $type;
         $status = $request->param('status','-1','int');
         if($status!=-1)
             $where['status'] = $status;
         $area = $request->param('area','','string');
         if(!empty($area))
             $where['address'] = ['like',$area.'%'];
         $this->assign('myarea',$area);
         $this->assign('mytype',$type);
         $this->assign('mystatus',$status);
         $page = $request->param('page',1,'int');
         $events = $eventModel->where($where)->paginate(4,false,array('page'=>$page));
         $this->assign('pager',$events->toArray());
         $types = Config::get('basketball.event_types');
         $this->assign('types',$types);
         $this->assign('status',Config::get('basketball.eventStatus'));
         $this->assign('showStatus',Config::get('basketball.show_eventStatus'));
         $provinces = Db::name('provinces')->select();
         $proData = array();
         foreach ($provinces as $item){
             $proData[$item['provinceid']] = $item['province'];
         }
         $this->assign('areas',$proData);
         $this->assign('title','赛事');
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
             ->order(['create_time'=>'desc'])
             ->paginate(6,false,['page'=>$page])
             ->toArray();
         $this->assign('pager',$clubs);
         $provinces = Db::name('provinces')->select();
         $proData = array();
         foreach ($provinces as $item){
             $proData[$item['provinceid']] = $item['province'];
         }
         $this->assign('provinces',$proData);
         $this->assign('name',$name);
         return $this->fetch();
     }

     //临时功能，补充球员球衣号码
//     public function clubno(){
//         $clubs = Db::name('club')->select();
//         Db::startTrans();
//         foreach ($clubs as $club){
//             $no = 4;
//             $players = json_decode($club['players'],true);
//             $playerno = [];
//             foreach ($players as $key=>$player){
//                 $playerno[$key] = $no;
//                 $no++;
//             }
//             $update = ['Id'=>$club['Id'],'players_no'=>json_encode($playerno)];
//             $res = Db::name('club')->update($update);
//             if(!$res){
//                 Db::rollback();
//                 return $this->returnJson('更新失败');
//             }
//         }
//         Db::commit();
//         return $this->returnJson('更新成功',true,1);
//     }
//    public function test(){
//         $test = [1,2,3,4,5,6];
//            $t = array_shift($test);
//            var_dump($t);
//            var_dump($test);
//    }


 }