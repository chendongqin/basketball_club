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
            ->order('starter','desc')
            ->select();
        $this->assign('homeData',$homeData);
        $visitingData = Db::name('player_data')
            ->where(['schedule_id'=>$scheduleId,'club_id'=>$schedule['visiting_team']])
            ->order('starter','desc')
            ->select();
        $this->assign('visitingData',$visitingData);
        $this->assign('title','球员数据');
        $this->assign('schedule',$schedule);
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
        $data = ['logs'=>$logs,'second'=>$schedule['second'],'homeScore'=>$schedule['home_score'],'visitingScore'=>$schedule['visiting_score'],];
        return $this->returnJson('更新',true,1,$data);
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
            foreach ($playerData['home'] as $key=>$home){
                $playerData['home'][$key]['player_name'] = idOfFiler('user',['Id'=>$home['user_id']]);
            }
            foreach ($playerData['visiting'] as $key=>$visiting){
                $playerData['visiting'][$key]['player_name'] = idOfFiler('user',['Id'=>$visiting['user_id']]);
            }
            Cache::set($this->_dataTimeKey.$scheduleId,time(),36000);
            $data = ['players'=>$playerData,'second'=>$schedule['second'],'homeScore'=>$schedule['home_score'],'visitingScore'=>$schedule['visiting_score'],];
            return $this->returnJson('更新',true,1,$data);
        }
        return $this->returnJson('未更新',false,1);
    }


    public function detail(){
        $userId = $this->request->param('userId',0,'int');
        $user = Db::name('user')->where('Id',$userId)->find();
        if(empty($user))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'用户不存在']);
        $datas = Db::name('player_data')->where(['user_id'=>$userId,'is_playing'=>2])->order('schedule_id','desc')->select();
        $total['score'] = $total['rebounds']= $total['assists']= $total['steals']= $total['blocks']= $total['lost']
            = $total['shoot']= $total['hit']= $total['three_shoot']= $total['three_hit']
            = $total['penalty_shoot']= $total['penalty_hit']= $total['foul']= $total['playing_time']=0;
        $showData = [];
        $i = 0;
//        $perData[] = 0;
        foreach ($datas as $data){
            foreach ($total as $key=>$value){
                $total[$key] +=$data[$key];
            }
            if($i<10)
                $showData[] = $data;
//                $perData[] = (($data['score'] +$data['rebounds'] +$data['assists']+$data['steals']+$data['blocks'])-($data['shoot']-$data['hit'])-($data['penalty_shoot']-$data['penalty_hit'])-$data['lost']);
            $i++;
        }
        /*计算这个效率准则的公式为：
        [(得分+篮板+助攻+抢断+封盖)-(出手次数-命中次数)-(罚球次数-罚球命中次数)-失误次数]/球员上场比赛的场次*/
        $count = count($datas)==0?1:count($datas);
        $per = (($total['score'] +$total['rebounds'] +$total['assists']+$total['steals']+$total['blocks'])-($total['shoot']-$total['hit'])-($total['penalty_shoot']-$total['penalty_hit'])-$total['lost'])/$count;
        $avg = [];
        foreach ($total as $key=>$value){
            $avg[$key] = (float)number_format($value/$count,1,'.','');
        }
        $clubNum = count(json_decode($user['club']));
        $this->assign('clubNum',$clubNum);
        $this->assign('count',$count);
        $this->assign('player',$user);
        $this->assign('datas',$showData);
        $this->assign('total',$total);
        $this->assign('avg',$avg);
        $this->assign('playerId',$userId);
        $this->assign('per',number_format($per,2,'.',''));
//        $this->assign('perData',$perData);
        $this->assign('title','球员数据预览');
        return $this->fetch();
    }

    public function per(){
        $userId = $this->request->param('userId',0,'int');
        $user = Db::name('user')->where('Id',$userId)->find();
        if(empty($user))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'用户不存在']);
        $datas = Db::name('player_data')->where(['user_id'=>$userId,'is_playing'=>2])->order('update_time','asc')->limit(10)->select();
        $perData = [];
        foreach ($datas as $data){
            $perData[] = (($data['score'] +$data['rebounds'] +$data['assists']+$data['steals']+$data['blocks'])-($data['shoot']-$data['hit'])-($data['penalty_shoot']-$data['penalty_hit'])-$data['lost']);
        }
        $num = count($datas)>=10?0:(10-count($datas));
        for ($i=0;$i<=$num;$i++){
            array_unshift($perData,0);
        }
        return $this->returnJson('成功',true,1,$perData);
    }

}