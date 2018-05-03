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

    private $_allow =['index','broadcastOut'];

    public function _initialize()
    {
        parent::_initialize();
        $act = $this->request->action();
        if(!in_array($act,$this->_allow)){
            $user = $this->getUser();
            $scheduleId = $this->request->param('id',0,'int');
            $schedule =Db::name('schedule')->where('Id',$scheduleId)->find();
            if(empty($schedule)){
                header('Content-type: application/json; charset=utf-8');
                echo json_encode(['msg'=>'比赛不存在','status'=>false,'code'=>0]);
                die();
            }
            $event = Db::name('event')->where('Id',$schedule['event_id'])->find();
            if(empty($event)) {
                header('Content-type: application/json; charset=utf-8');
                echo json_encode(['msg'=>'赛事不存在','status'=>false,'code'=>0]);
                die();
            }
            if($schedule['broadcast']!=$user['Id']){
                header('Content-type: application/json; charset=utf-8');
                echo json_encode(['msg'=>'您不是当前直播员！','status'=>false,'code'=>0]);
                die();
            }
        }
    }

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
            return $this->fetch('setstart');
        return $this->fetch();
    }
    //首发设置
    public function setStart(){
        $id = $this->request->param('id',0,'int');
        $schedule = Db::name('schedule')->where('Id',$id)->find();
        if(empty($schedule))
            return $this->returnJson('比赛不存在');
        $homeStarts = $this->request->param('homeStarts','','string');
        $visitingStarts = $this->request->param('visitingStarts','','string');
        $homeStarts = explode(',',$homeStarts);
        if(count($homeStarts)!=5)
            return $this->returnJson('主队首发参数不正确');
        $visitingStarts = explode(',',$visitingStarts);
        if(count($visitingStarts)!=5)
            return $this->returnJson('客队首发参数不正确');
        $home = Db::name('club')->where('Id',$schedule['home_team'])->find();
        if(empty($home))
            return $this->returnJson('主队不存在');
        $visiting = Db::name('club')->where('Id',$schedule['visiting_team'])->find();
        if(empty($visiting))
            return $this->returnJson('客队不存在');
        Db::startTrans();
        $homePlayers = json_decode($home['players'],true);
        $visitingPlayers = json_decode($visiting['players'],true);
        $add = ['schedule_id'=>$id,'event_id'=>$schedule['event_id']];
        $add['club_id'] = $schedule['home_team'];
        foreach ($homePlayers as $userId=> $homePlayer){
            $add['user_id'] = $userId;
            if(in_array($userId,$homeStarts)){
                $add['starter'] = 1;
                $add['is_playing'] = 1;
            }
            $res = Db::name('player_data')->insert($add);
            if(!$res){
                Db::rollback();
                return $this->returnJson('主队队员比赛数据初始化失败');
            }
        }
        $add['club_id'] = $schedule['visiting_team'];
        foreach ($visitingPlayers as $userId=> $visitingPlayer){
            $add['user_id'] = $userId;
            if(in_array($userId,$visitingStarts))
            {
                $add['starter'] = 1;
                $add['is_playing'] = 1;
            }
            $res = Db::name('player_data')->insert($add);
            if(!$res){
                Db::rollback();
                return $this->returnJson('客队队队员比赛数据初始化失败');
            }
        }
        $update = ['Id'=>$id,'acting'=>2];
        $Upres = Db::name('schedule')->update($update);
        if(!$Upres){
            Db::rollback();
            return $this->returnJson('更改比赛状态失败，请重试!');
        }
        Db::commit();
        return $this->returnJson('设置成功',true,1);
    }
    //篮板
    public function rebounds(){
        $scheduleId = $this->request->param('id',0,'int');
        $playerId = $this->request->param('playerId',0,'int');
        $hometeam = $this->request->param('hometeam',1,'int');
        $team = $hometeam==1?'[主队]':'[客队]';
        $player = Db::name('user')->where('Id',$playerId)->find();
        if(empty($player))
            return $this->returnJson('球员不存在');
        $playerData = Db::name('player_data')->where(['schedule_id'=>$scheduleId,'user_id'=>$playerId])->find();
        if(empty($playerData))
            return $this->returnJson('该球员没有参加该比赛数据');
        $update = ['Id'=>$playerData['Id'],'rebounds'=>$playerData['rebounds']+1];
        Db::startTrans();
        $res = Db::name('player_data')->update($update);
        if(!$res)
            return $this->returnJson('失败，请重试！');
        $logs = json_decode($playerData['logs'],true);
        $logs = empty($logs)?array():$logs;
        $logs_act = json_decode($playerData['logs_act'],true);
        $logs_act = empty($logs_act)?array():$logs_act;
        array_unshift($logs,$team.' '.$player['name'].' 抢到篮板');
        array_unshift($logs_act,[$playerId=>'rebounds']);
        $scheduleUpdate = ['Id'=>$scheduleId,'update_time'=>time(),'logs'=>json_encode($logs),'logs_act'=>json_encode($logs_act)];
        $scheduleUpRes = Db::name('schedule')->update($scheduleUpdate);
        if(!$scheduleUpRes){
            Db::rollback();
            return $this->returnJson('失败，请重试！');
        }
        Db::commit();
        return $this->returnJson('成功',true,1);
    }




    //赛事管理员退出
    public function broadcastOut(){
        $scheduleId = $this->request->param('id',0,'int');
        $schedule = Db::name('schedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->returnJson('比赛不存在');
        $update = ['Id'=>$scheduleId,'broadcast'=>0];
        $res = Db::name('schedule')->update($update);
        if(!$res)
            return $this->returnJson('退出失败，请重试!');
        return $this->returnJson('退出成功',true,1);
    }
}
