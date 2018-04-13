<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/31
 * Time: 9:15
 */
namespace app\index\controller;
use base\Base;
use think\Db;
use think\Config;

class Event extends Base{

    public function index(){
        $id = $this->request->param('id','','int');
        $event = Db::name('event')->where('Id',$id)->find();
        if(empty($event))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'赛事不存在']);
        $user = Db::name('user')->where('Id',$event['create_user'])->find();
        $joinClubs = json_decode($event['join_clubs'],true);
        $types = Config::get('basketball.event_types');
        $statusStrs = Config::get('basketball.event_status');
        $this->assign('event',$event);
        $this->assign('createUser',$user);
        $this->assign('types',$types);
        $this->assign('statusStrs',$statusStrs);
        $this->assign('joinClubs',$joinClubs);
        $schedules = Db::name('schedule')
            ->where('event_id',$id)
            ->order(['over','game_time'=>'asc'])
            ->select();
        $this->assign('schedules',$schedules);
        return $this->fetch();
    }

//    public function schedule(){
//        $id = $this->request->param('id','','int');
//        $event = Db::name('event')->where('Id',$id)->find();
//        if(empty($event))
//            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'赛事不存在']);
//        $schedules = Db::name('schedule')
//            ->where('event_id',$id)
//            ->order(['over','game_time'=>'desc'])
//            ->select();
//        $this->assign('event',$event);
//        $this->assign('schedules',$schedules);
//        return $this->fetch();
//    }



}