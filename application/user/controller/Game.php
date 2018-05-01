<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/5/1
 * Time: 10:41
 */
namespace app\user\controller;
use base\Userbase;
use think\Db;
class Game extends Userbase{

    public function index(){
        $user = $this->getUser();
        $scheduleId = $this->request->param('id',0,'int');
        $schedule = Db::name('schedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'比赛不存在']);
        $event = Db::name('event')->where('Id',$schedule['event_id'])->find();
        if(empty($event))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'赛事不存在']);
        if($event['create_user']!=$user['Id']){
            $worker = Db::name('event_workers')->where(['event_id'=>$event['Id'],'user_id'=>$user['Id']]);
            if(empty($worker))
                return  $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'您没有操作的权限']);
        }
        if($schedule['broadcast']!==0 and $schedule['broadcast']!=$user['Id']){
            $broadcastName = Db::name('user')->where('Id',$schedule['broadcast'])->column('name');
            if(!empty($broadcastName))
                return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'比赛已被'.$broadcastName[0].'操作']);
        }
        if($schedule['broadcast']!=$user['Id']){
            $update = ['Id'=>$scheduleId,'broadcast'=>$user['Id']];
            $res = Db::name('schedule')->update($update);
            if(!$res)
                return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'失败，请重新进入']);
        }
        $this->assign('event',$event);
        $this->assign('schedule',$schedule);
        $home = Db::name('club')->where('Id',$schedule['home_team'])->find();
        if(empty($home))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'主队不存在']);
        $this->assign('home',$home);
        $visiting = Db::name('club')->where('Id',$schedule['visiting_team'])->find();
        if(empty($visiting))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'客队不存在']);
        $this->assign('visiting',$visiting);
        $homePlays = json_decode($home['players'],true);
        $this->assign('homePlayes',$homePlays);
        $visitingPlays = json_decode($visiting['players'],true);
        $this->assign('visitingPlayes',$visitingPlays);
        if($schedule['acting'] === 0)
            return $this->fetch('setstart.phmtl');
        return $this->fetch();
    }

    public function setStart(){

    }

    public function broadcastOut(){
        $scheduleId = $this->request->param('id',0,'int');
        $schedule = Db::name('schedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->returnJson('比赛不存在');

    }

}
