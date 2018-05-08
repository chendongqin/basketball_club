<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/5/5
 * Time: 11:04
 */
namespace app\index\controller;
use base\Base;
use think\Db;
use think\Cache;

class Game extends Base{

    private $_logKey = 'schedule.logs.update.time';
    private $_dataTimeKey = 'schedule.player.data.update.time';

    public function index(){
        $scheduleId = $this->request->param('id',0,'int');
        $schedule = Db::name('schedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'比赛不存在']);
        if($schedule['acting'] === 0)
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'比赛未开始']);
        $this->assign('schedule',$schedule);
        $gameLogs = json_decode($schedule['logs'],true);
        $this->assign('gameLogs',$gameLogs);
        $home = Db::name('club')->where('Id',$schedule['home_team'])->find();
        $visiting = Db::name('club')->where('Id',$schedule['visiting_team'])->find();
        $this->assign('home',$home);
        $this->assign('visiting',$visiting);
        $homePlayers = json_decode($home['players'],true);
        $this->assign('homePlayers',$homePlayers);
        $visitingPlayers = json_decode($visiting['players'],true);
        $this->assign('visitingPlayers',$visitingPlayers);
        $this->assign('title',$home['name'].'VS'.$visiting['name']);
        return $this->fetch();
    }

    public function playerData(){
        $scheduleId = $this->request->param('id',0,'int');
        $schedule = Db::name('schedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'比赛不存在']);
        if($schedule['acting'] === 0)
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'比赛未开始']);
        $this->assign('schedule',$schedule);
        $home = Db::name('club')->where('Id',$schedule['home_team'])->find();
        $visiting = Db::name('club')->where('Id',$schedule['visiting_team'])->find();
        $this->assign('home',$home);
        $this->assign('visiting',$visiting);
        $homeData = Db::name('player_data')
            ->where(['schedule_id'=>$scheduleId,'club_id'=>$schedule['home_team']])
            ->select();
        $this->assign('homeData',$homeData);
        $visitingData = Db::name('player_data')
            ->where(['schedule_id'=>$scheduleId,'club_id'=>$schedule['visiting_team']])
            ->select();
        $this->assign('visitingData',$visitingData);
        $this->assign('title','球员数据');
        return $this->fetch();
    }

    public function uplogs(){
        $scheduleId = $this->request->param('id',0,'int');
        $schedule = Db::name('schedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->returnJson('比赛不存在!');
        if($schedule['acting'] === 0)
            return $this->returnJson('比赛未开始!');
        $oldTime = Cache::get($this->_logKey.$scheduleId);
        if($oldTime===false)
            $oldTime = 0;
        if($oldTime>=$schedule['update_time']){
            return $this->returnJson('未更新',false,1);
        }
        $logs = json_decode($schedule['logs'],true);
        Cache::set($this->_logKey.$scheduleId,time(),36000);
        return $this->returnJson('更新',true,1,$logs);
    }

    public function upplayerData(){
        $scheduleId = $this->request->param('id',0,'int');
        $schedule = Db::name('schedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->returnJson('比赛不存在!');
        if($schedule['acting'] === 0)
            return $this->returnJson('比赛未开始!');
        $oldTime = Cache::get($this->_dataTimeKey.$scheduleId);
        if($oldTime===false or $oldTime<$schedule['update_time']){
            $playerData = [];
            $playerData['home']=Db::name('player_data')->where(['schedule_id'=>$scheduleId,'club_id'=>$schedule['home_team']])->order(['starter'=>'desc'])->select();
            $playerData['visiting']=Db::name('player_data')->where(['schedule_id'=>$scheduleId,'club_id'=>$schedule['visiting_team']])->order(['starter'=>'desc'])->select();
//            Cache::set($this->_dataTimeKey.$scheduleId,time(),36000);
            return $this->returnJson('成功',true,1,$playerData);
        }
        return $this->returnJson('未更新',false,1);
    }


    public function detail(){
        $userId = $this->request->param('userId',0,'int');
        $user = Db::name('user')->where('Id',$userId)->find();
        if(empty($user))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'用户不存在']);
        $datas = Db::name('player_data')->where(['user_id'=>$userId,'is_playing'=>2])->select();
        $total['score'] = $total['rebounds']= $total['assists']= $total['steals']= $total['blocks']= $total['lost']
            = $total['shoot']= $total['hit']= $total['three_shoot']= $total['three_hit']
            = $total['penalty_shoot']= $total['penalty_hit']= $total['foul']= $total['playing_time']=0;
        foreach ($datas as $data){
            foreach ($total as $key=>$value){
                $total[$key] = $total[$key]+$data[$key];
            }
        }
        $avg = [];
        $count = count($datas);
        foreach ($total as $key=>$value){
            $avg[$key] = number_format($value/$count,2,'.','');
        }
        $this->assign('datas',$datas);
        $this->assign('total',$total);
        $this->assign('avg',$avg);
        return $this->fetch();
    }


}