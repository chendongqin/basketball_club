<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/5/1
 * Time: 10:41
 */
namespace app\user\controller;
use base\Userbase;
use think\Config;
use think\Db;
class Game extends Userbase{

    private $_allow =['index','broadcastOut'];

    //比赛ID传输字段统一用id作为验证
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
            if($schedule['acting']==3){
                header('Content-type: application/json; charset=utf-8');
                echo json_encode(['msg'=>'比赛已结束，不可操作！','status'=>false,'code'=>0]);
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
        if(count($homePlays)<7)
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'主队队员少于7人']);
        $this->assign('homePlayers',$homePlays);
        $visitingPlays = json_decode($visiting['players'],true);
        if(count($visitingPlays)<7)
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'客队队员少于7人']);
        $this->assign('visitingPlayers',$visitingPlays);
        $homeNo = json_decode($home['players_no'],true);
        $this->assign('homeNo',$homeNo);
        $visitingNo = json_decode($visiting['players_no'],true);
        $this->assign('visitingNo',$visitingNo);
        if($schedule['acting'] === 0){
            $this->assign('title','首发设置');
            return $this->fetch('setstart');
        }
        $playsStatus = [];
        foreach ($homePlays as $key=>$play){
            $playStatus = Db::name('player_data')->where(['user_id'=>$key,'schedule_id'=>$scheduleId])->find();
            $playsStatus[$key]['is_playing'] = $playStatus['is_playing'];
            $playsStatus[$key]['starter'] = $playStatus['starter'];
        }
        foreach ($visitingPlays as $key=>$play){
            $playStatus = Db::name('player_data')->where(['user_id'=>$key,'schedule_id'=>$scheduleId])->find();
            $playsStatus[$key]['is_playing'] = $playStatus['is_playing'];
            $playsStatus[$key]['starter'] = $playStatus['starter'];
        }
        $this->assign('playerStatus',$playsStatus);
        $this->assign('logs',json_decode($schedule['logs'],true));
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
        $homeStarts = trim($homeStarts,',');
        $visitingStarts = trim($visitingStarts,',');
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
            }else{
                $add['starter'] = 0;
                $add['is_playing'] = 0;
            }
            $res = Db::name('player_data')->insert($add);
            if(!$res){
                Db::rollback();
                return $this->returnJson('主队队员比赛数据初始化失败');
            }
        }
        $add['club_id'] = $schedule['visiting_team'];
        $unqiute = Db::name('player_data')->where(['schedule_id'=>$id,'user_id'=>['in',$visitingPlayers]])->find();
        if(!empty($unqiute)){
            Db::rollback();
            return $this->returnJson('客队与主队存在了相同队员,请协商处理！');
        }
        foreach ($visitingPlayers as $userId=> $visitingPlayer){
            $add['user_id'] = $userId;
            if(in_array($userId,$visitingStarts))
            {
                $add['starter'] = 1;
                $add['is_playing'] = 1;
            }else{
                $add['starter'] = 0;
                $add['is_playing'] = 0;
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

    /*
     * 2分
     * type查看配置
    */
    public function getTwo(){
        $scheduleId = $this->request->param('id',0,'int');
        $playerId = $this->request->param('playerId',0,'int');
        $hometeam = $this->request->param('hometeam',1,'int');
        $type = $this->request->param('type',0,'int');
        $team = $hometeam==1?'[主队]':'[客队]';
        $player = Db::name('user')->where('Id',$playerId)->find();
        if(empty($player))
            return $this->returnJson('球员不存在');
        $typeStr = Config::get('basketball.two_score');
        if(!isset($typeStr[$type]))
            return $this->returnJson('参数错误');
        $playerData = Db::name('player_data')->where(['schedule_id'=>$scheduleId,'user_id'=>$playerId])->find();
        if(empty($playerData))
            return $this->returnJson('该球员没有参加该比赛数据');
        $schedule = Db::name('shcedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->returnJson('比赛不存在');
        $logs = json_decode($schedule['logs'],true);
        $logs = empty($logs)?array():$logs;
        $logs_act = json_decode($schedule['logs_act'],true);
        $logs_act = empty($logs_act)?array():$logs_act;
        Db::startTrans();
        if($type>=0 and $type <4){
            $update = ['Id'=>$playerData['Id'],'update_time'=>time(),'score'=>$playerData['score']+2,'shoot'=>$player['shoot']+1,'hit'=>$playerData['hit']+1];
            $res = Db::name('player_data')->update($update);
            if(!$res)
                return $this->returnJson('失败，请重试！');
            array_unshift($logs_act,[$playerId=>'hit']);
        }else{
            $update = ['Id'=>$playerData['Id'],'update_time'=>time(),'shoot'=>$player['shoot']+1];
            $res = Db::name('player_data')->update($update);
            if(!$res)
                return $this->returnJson('失败，请重试！');
            array_unshift($logs_act,[$playerId=>'shoot']);
        }
        array_unshift($logs,$team.' '.$player['name'].' '.$typeStr[$type]);
        $scheduleUpdate = ['Id'=>$scheduleId,'update_time'=>time(),'logs'=>json_encode($logs),'logs_act'=>json_encode($logs_act)];
        $scheduleUpRes = Db::name('schedule')->update($scheduleUpdate);
        if(!$scheduleUpRes){
            Db::rollback();
            return $this->returnJson('失败，请重试！');
        }
        Db::commit();
        return $this->returnJson('成功',true,1);
    }

    /**
     * 3分
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\
     * type 0不进|1进球
     */
    public function getThree(){
        $scheduleId = $this->request->param('id',0,'int');
        $playerId = $this->request->param('playerId',0,'int');
        $hometeam = $this->request->param('hometeam',1,'int');
        $type = (int)$this->request->param('type',0,'int');
        $team = $hometeam==1?'[主队]':'[客队]';
        $player = Db::name('user')->where('Id',$playerId)->find();
        if(empty($player))
            return $this->returnJson('球员不存在');
        $playerData = Db::name('player_data')->where(['schedule_id'=>$scheduleId,'user_id'=>$playerId])->find();
        if(empty($playerData))
            return $this->returnJson('该球员没有参加该比赛数据');
        $schedule = Db::name('shcedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->returnJson('比赛不存在');
        $logs = json_decode($schedule['logs'],true);
        $logs = empty($logs)?array():$logs;
        $logs_act = json_decode($schedule['logs_act'],true);
        $logs_act = empty($logs_act)?array():$logs_act;
        Db::startTrans();
        if($type===1){
            $update = ['Id'=>$playerData['Id'],'score'=>$playerData['score']+3,'three_shoot'=>$player['three_shoot']+1,'three_hit'=>$playerData['three_hit']+1];
            $res = Db::name('player_data')->update($update);
            if(!$res)
                return $this->returnJson('失败，请重试！');
            array_unshift($logs_act,[$playerId=>'three_hit']);
            array_unshift($logs,$team.' '.$player['name'].' 命中三分');
        }else{
            $update = ['Id'=>$playerData['Id'],'update_time'=>time(),'three_shoot'=>$player['three_shoot']+1];
            $res = Db::name('player_data')->update($update);
            if(!$res)
                return $this->returnJson('失败，请重试！');
            array_unshift($logs_act,[$playerId=>'three_shoot']);
            array_unshift($logs,$team.' '.$player['name'].' 三分打铁');
        }
        $scheduleUpdate = ['Id'=>$scheduleId,'update_time'=>time(),'logs'=>json_encode($logs),'logs_act'=>json_encode($logs_act)];
        $scheduleUpRes = Db::name('schedule')->update($scheduleUpdate);
        if(!$scheduleUpRes){
            Db::rollback();
            return $this->returnJson('失败，请重试！');
        }
        Db::commit();
        return $this->returnJson('成功',true,1);
    }

    /**
     * 罚球
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\
     * type 0不进|1进球
     */
    public function getOne(){
        $scheduleId = $this->request->param('id',0,'int');
        $playerId = $this->request->param('playerId',0,'int');
        $hometeam = $this->request->param('hometeam',1,'int');
        $type = (int)$this->request->param('type',0,'int');
        $team = $hometeam==1?'[主队]':'[客队]';
        $player = Db::name('user')->where('Id',$playerId)->find();
        if(empty($player))
            return $this->returnJson('球员不存在');
        $playerData = Db::name('player_data')->where(['schedule_id'=>$scheduleId,'user_id'=>$playerId])->find();
        if(empty($playerData))
            return $this->returnJson('该球员没有参加该比赛数据');
        $schedule = Db::name('shcedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->returnJson('比赛不存在');
        $logs = json_decode($schedule['logs'],true);
        $logs = empty($logs)?array():$logs;
        $logs_act = json_decode($schedule['logs_act'],true);
        $logs_act = empty($logs_act)?array():$logs_act;
        Db::startTrans();
        if($type===1){
            $update = ['Id'=>$playerData['Id'],'update_time'=>time(),'score'=>$playerData['score']+1,'penalty_shoot'=>$player['penalty_shoot']+1,'penalty_hit'=>$playerData['penalty_hit']+1];
            $res = Db::name('player_data')->update($update);
            if(!$res)
                return $this->returnJson('失败，请重试！');
            array_unshift($logs_act,[$playerId=>'penalty_hit']);
            array_unshift($logs,$team.' '.$player['name'].' 命中罚球');
        }else{
            $update = ['Id'=>$playerData['Id'],'update_time'=>time(),'penalty_shoot'=>$player['penalty_shoot']+1];
            $res = Db::name('player_data')->update($update);
            if(!$res)
                return $this->returnJson('失败，请重试！');
            array_unshift($logs_act,[$playerId=>'penalty_shoot']);
            array_unshift($logs,$team.' '.$player['name'].' 罚球不中');
        }
        $scheduleUpdate = ['Id'=>$scheduleId,'update_time'=>time(),'logs'=>json_encode($logs),'logs_act'=>json_encode($logs_act)];
        $scheduleUpRes = Db::name('schedule')->update($scheduleUpdate);
        if(!$scheduleUpRes){
            Db::rollback();
            return $this->returnJson('失败，请重试！');
        }
        Db::commit();
        return $this->returnJson('成功',true,1);
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
        $update = ['Id'=>$playerData['Id'],'update_time'=>time(),'rebounds'=>$playerData['rebounds']+1];
        Db::startTrans();
        $res = Db::name('player_data')->update($update);
        if(!$res)
            return $this->returnJson('失败，请重试！');
        $schedule = Db::name('shcedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->returnJson('比赛不存在');
        $logs = json_decode($schedule['logs'],true);
        $logs = empty($logs)?array():$logs;
        $logs_act = json_decode($schedule['logs_act'],true);
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

    /**
     * 犯规
     * type =0=>普通犯规| 1=>犯规罚球|2=>2+1| 3=> 3+1
     */
    public function faul(){
        $scheduleId = $this->request->param('id',0,'int');
        $playerId = $this->request->param('playerId',0,'int');
        //犯规者
        $foulId = $this->request->param('foulId',0,'int');
        $hometeam = $this->request->param('hometeam',1,'int');
        $type = $this->request->param('type',0,'int');
        $team = $hometeam==1?'[主队]':'[客队]';
        //犯规队伍
        $foulTeam = $hometeam==0?'[主队]':'[客队]';
        $player = Db::name('user')->where('Id',$playerId)->find();
        $foulPlayer = Db::name('user')->where('Id',$foulId)->find();
        if(empty($player))
            return $this->returnJson('球员不存在');
        if(empty($foulPlayer))
            return $this->returnJson('犯规球员不存在');
        if($type<0 or $type>3)
            return $this->returnJson('参数错误');
        $playerData = Db::name('player_data')->where(['schedule_id'=>$scheduleId,'user_id'=>$playerId])->find();
        $foulPlayerData = Db::name('player_data')->where(['schedule_id'=>$scheduleId,'user_id'=>$foulId])->find();
        if(empty($playerData))
            return $this->returnJson('该球员没有参加该比赛数据');
        if(empty($foulPlayerData))
            return $this->returnJson('该规范球员没有参加该比赛数据');
        $schedule = Db::name('shcedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->returnJson('比赛不存在');
        $logs = json_decode($schedule['logs'],true);
        $logs = empty($logs)?array():$logs;
        $logs_act = json_decode($schedule['logs_act'],true);
        $logs_act = empty($logs_act)?array():$logs_act;
        Db::startTrans();
        if($type===2){
            $update = ['Id'=>$playerData['Id'],'update_time'=>time(),'score'=>$playerData['score']+2,'shoot'=>$player['shoot']+1,'hit'=>$playerData['hit']+1];
            $res = Db::name('player_data')->update($update);
            if(!$res)
                return $this->returnJson('失败，请重试！');
            array_unshift($logs_act,[$playerId=>'two_andone',$foulId=>'foul']);
            array_unshift($logs,$team.''.$player['name'].' 造成'.$foulTeam.$foulPlayer['name'].'犯规,球进！加罚一次!');
        }elseif($type===3){
            $update = ['Id'=>$playerData['Id'],'update_time'=>time(),'score'=>$playerData['score']+3,'three_shoot'=>$player['three_shoot']+1,'three_hit'=>$playerData['three_hit']+1];
            $res = Db::name('player_data')->update($update);
            if(!$res)
                return $this->returnJson('失败，请重试！');
            array_unshift($logs_act,[$playerId=>'three_andone',$foulId=>'foul']);
            array_unshift($logs,$team.''.$player['name'].' 三分出手造成'.$foulTeam.$foulPlayer['name'].'犯规,球进！加罚一次!');
        }elseif($type===1){
            array_unshift($logs_act,[$foulId=>'foul']);
            array_unshift($logs,$team.''.$player['name'].' 造成'.$foulTeam.$foulPlayer['name'].'犯规！罚球两次!');
        }else{
            array_unshift($logs_act,[$foulId=>'foul']);
            array_unshift($logs,$team.''.$player['name'].' 造成'.$foulTeam.$foulPlayer['name'].'犯规！前场球！');
        }
        $foulUpdate = ['Id'=>$foulPlayerData['Id'],'foul'=>$foulPlayerData['foul']+1,'update_time'=>time()];
        $foulRes = Db::name('player_data')->update($foulUpdate);
        if(!$foulRes){
            Db::rollback();
            return $this->returnJson('失败，请重试！');
        }
        $scheduleUpdate = ['Id'=>$scheduleId,'update_time'=>time(),'logs'=>json_encode($logs),'logs_act'=>json_encode($logs_act)];
        if($hometeam ==0){
            $str = 'home_foul';
            $fouls = json_decode($schedule['home_foul'],true);
        }else{
            $str = 'visiting_foul';
            $fouls = json_decode($schedule['visiting_foul'],true);
        }
        $fouls = empty($fouls)?[]:$fouls;
        $fouls[$schedule['section']] = isset($fouls[$schedule['section']])?$fouls[$schedule['section']]+1:1;
        $scheduleUpdate[$str] = json_encode($fouls);
        $scheduleUpRes = Db::name('schedule')->update($scheduleUpdate);
        if(!$scheduleUpRes){
            Db::rollback();
            return $this->returnJson('失败，请重试！');
        }
        Db::commit();
        return $this->returnJson('成功',true,1);
    }

    //助攻
    public function assists(){
        $scheduleId = $this->request->param('id',0,'int');
        $playerId = $this->request->param('playerId',0,'int');
        //得分者
        $scoreId = $this->request->param('scoreId',0,'int');
        $hometeam = $this->request->param('hometeam',1,'int');
        $type = $this->request->param('type',0,'int');
        $team = $hometeam==1?'[主队]':'[客队]';
        $player = Db::name('user')->where('Id',$playerId)->find();
        $scorePlayer = Db::name('user')->where('Id',$scoreId)->find();
        if(empty($player))
            return $this->returnJson('球员不存在');
        if(empty($scorePlayer))
            return $this->returnJson('助攻得分的球员不存在');
        $playerData = Db::name('player_data')->where(['schedule_id'=>$scheduleId,'user_id'=>$playerId])->find();
        $scorePlayerData = Db::name('player_data')->where(['schedule_id'=>$scheduleId,'user_id'=>$scoreId])->find();
        if(empty($playerData))
            return $this->returnJson('该球员没有参加该比赛数据');
        if(empty($scorePlayerData))
            return $this->returnJson('该得分球员没有参加该比赛数据');
        $schedule = Db::name('shcedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->returnJson('比赛不存在');
        $logs = json_decode($schedule['logs'],true);
        $logs = empty($logs)?array():$logs;
        $logs_act = json_decode($schedule['logs_act'],true);
        $logs_act = empty($logs_act)?array():$logs_act;
        if($type ==1){
            $scoreUpdate = ['Id'=>$scorePlayerData['Id'],'update_time'=>time(),'score'=>$scorePlayerData['score']+3,'three_shoot'=>$scorePlayerData['three_shoot']+1,'three_hit'=>$scorePlayerData['three_hit']];
            array_unshift($logs_act,[$scoreId=>'three_hit',$playerId=>'assists']);
            array_unshift($logs,$team.''.$player['name'].' 把球传给'.$team.$scorePlayer['name'].',三分线外出手!'."\n稳稳命中!");
        }else{
            $scoreUpdate = ['Id'=>$scorePlayerData['Id'],'update_time'=>time(),'score'=>$scorePlayerData['score']+2,'shoot'=>$scorePlayerData['shoot']+1,'hit'=>$scorePlayerData['hit']];
            array_unshift($logs_act,[$scoreId=>'hit',$playerId=>'assists']);
            array_unshift($logs,$team.''.$player['name'].' 把球传给'.$team.$scorePlayer['name'].'!'."\n".$team.$scorePlayer['name'].'稳稳命中!');
        }
        Db::startTrans();
        $scoreRes = Db::name('player_data')->update($scoreUpdate);
        if(!$scoreRes){
            return $this->returnJson('失败，请重试');
        }
        $update = ['Id'=>$playerData['Id'],'update_time'=>time(),'rebounds'=>$playerData['rebounds']+1];
        $res = Db::name('player_data')->update($update);
        if(!$res){
            Db::rollback();
            return $this->returnJson('失败，请重试！');
        }
        $scheduleUpdate = ['Id'=>$scheduleId,'update_time'=>time(),'logs'=>json_encode($logs),'logs_act'=>json_encode($logs_act)];
        $scheduleUpRes = Db::name('schedule')->update($scheduleUpdate);
        if(!$scheduleUpRes){
            Db::rollback();
            return $this->returnJson('失败，请重试！');
        }
        Db::commit();
        return $this->returnJson('成功',true,1);
    }

    //抢断
    public function steals(){
        $scheduleId = $this->request->param('id',0,'int');
        $playerId = $this->request->param('playerId',0,'int');
        //被抢断者
        $stealsId = $this->request->param('stealsId',0,'int');
        $hometeam = $this->request->param('hometeam',1,'int');
        $team = $hometeam==1?'[主队]':'[客队]';
        $stealsteam = $hometeam==0?'[主队]':'[客队]';
        $player = Db::name('user')->where('Id',$playerId)->find();
        $stealsPlayer = Db::name('user')->where('Id',$stealsId)->find();
        if(empty($player))
            return $this->returnJson('球员不存在');
        if(empty($stealsPlayer))
            return $this->returnJson('被抢断的球员不存在');
        $playerData = Db::name('player_data')->where(['schedule_id'=>$scheduleId,'user_id'=>$playerId])->find();
        $stealsPlayerData = Db::name('player_data')->where(['schedule_id'=>$scheduleId,'user_id'=>$stealsId])->find();
        if(empty($playerData))
            return $this->returnJson('该球员没有参加该比赛数据');
        if(empty($stealsPlayerData))
            return $this->returnJson('被抢断球员没有参加该比赛数据');
        $schedule = Db::name('shcedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->returnJson('比赛不存在');
        $logs = json_decode($schedule['logs'],true);
        $logs = empty($logs)?array():$logs;
        $logs_act = json_decode($schedule['logs_act'],true);
        $logs_act = empty($logs_act)?array():$logs_act;
        Db::startTrans();
        //失误处理
        $stealsUpdate = ['Id'=>$stealsPlayerData['Id'],'update_time'=>time(),'lost'=>$stealsPlayerData['lost']+1];
        $stealRes = Db::name('players_data')->update($stealsUpdate);
        if(!$stealRes)
            return $this->returnJson('失败，请重试');
        //抢断处理
        $update = ['Id'=>$playerData['Id'],'update_time'=>time(),'steals'=>$playerData['steals']+1];
        $res = Db::name('player_data')->update($update);
        if(!$res){
            Db::rollback();
            return $this->returnJson('失败，请重试');
        }
        //直播设置以及回滚预处理
        array_unshift($logs_act,[$stealsId=>'lost',$playerId=>'steals']);
        array_unshift($logs,$team.''.$player['name'].' 死亡缠绕'.$stealsteam.$stealsPlayer['name'].'，把球断下');
        $scheduleUpdate = ['Id'=>$scheduleId,'update_time'=>time(),'logs'=>json_encode($logs),'logs_act'=>json_encode($logs_act)];
        $scheduleUpRes = Db::name('schedule')->update($scheduleUpdate);
        if(!$scheduleUpRes){
            Db::rollback();
            return $this->returnJson('失败，请重试！');
        }
        Db::commit();
        return $this->returnJson('成功',true,1);
    }

    //blocks 盖帽 type=1三分被盖
    public function blocks(){
        $scheduleId = $this->request->param('id',0,'int');
        $playerId = $this->request->param('playerId',0,'int');
        //被抢断者
        $blocksId = $this->request->param('blocksId',0,'int');
        $hometeam = $this->request->param('hometeam',1,'int');
        $type = $this->request->param('type',0,'int');
        $team = $hometeam==1?'[主队]':'[客队]';
        $stealsteam = $hometeam==0?'[主队]':'[客队]';
        $player = Db::name('user')->where('Id',$playerId)->find();
        $blocksPlayer = Db::name('user')->where('Id',$blocksId)->find();
        if(empty($player))
            return $this->returnJson('球员不存在');
        if(empty($blocksPlayer))
            return $this->returnJson('被盖帽的球员不存在');
        $playerData = Db::name('player_data')->where(['schedule_id'=>$scheduleId,'user_id'=>$playerId])->find();
        $blocksPlayerData = Db::name('player_data')->where(['schedule_id'=>$scheduleId,'user_id'=>$blocksId])->find();
        if(empty($playerData))
            return $this->returnJson('该球员没有参加该比赛数据');
        if(empty($blocksPlayerData))
            return $this->returnJson('被盖帽球员没有参加该比赛数据');
        $schedule = Db::name('shcedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->returnJson('比赛不存在');
        $logs = json_decode($schedule['logs'],true);
        $logs = empty($logs)?array():$logs;
        $logs_act = json_decode($schedule['logs_act'],true);
        $logs_act = empty($logs_act)?array():$logs_act;
        Db::startTrans();
        //盖帽处理
        $blocksUpdate = ['Id'=>$blocksPlayerData['Id'],'update_time'=>time()];
        if($type==1){
            $blocksUpdate['three_shoot'] = $blocksPlayerData['three_shoot']+1;
            $logs_str = '三分出手';
            $act = [$blocksId=>'three_shoot',$playerId=>'blocks'];
        }else{
            $blocksUpdate['shoot'] = $blocksPlayerData['shoot']+1;
            $logs_str = '出手';
            $act = [$blocksId=>'shoot',$playerId=>'blocks'];
        }
        $blocksRes = Db::name('players_data')->update($blocksUpdate);
        if(!$blocksRes)
            return $this->returnJson('失败，请重试');
        //抢断处理
        $update = ['Id'=>$playerData['Id'],'update_time'=>time(),'blocks'=>$playerData['blocks']+1];
        $res = Db::name('player_data')->update($update);
        if(!$res){
            Db::rollback();
            return $this->returnJson('失败，请重试');
        }
        //直播设置以及回滚预处理
        array_unshift($logs_act,$act);
        array_unshift($logs,$stealsteam.$blocksPlayer['name'].$logs_str.'被'.$team.''.$player['name'].' 直接送了一个大火锅!');
        $scheduleUpdate = ['Id'=>$scheduleId,'update_time'=>time(),'logs'=>json_encode($logs),'logs_act'=>json_encode($logs_act)];
        $scheduleUpRes = Db::name('schedule')->update($scheduleUpdate);
        if(!$scheduleUpRes){
            Db::rollback();
            return $this->returnJson('失败，请重试！');
        }
        Db::commit();
        return $this->returnJson('成功',true,1);
    }

    //失误
    public function lost(){
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
        $update = ['Id'=>$playerData['Id'],'update_time'=>time(),'lost'=>$playerData['lost']+1];
        Db::startTrans();
        $res = Db::name('player_data')->update($update);
        if(!$res)
            return $this->returnJson('失败，请重试！');
        $schedule = Db::name('shcedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->returnJson('比赛不存在');
        $logs = json_decode($schedule['logs'],true);
        $logs = empty($logs)?array():$logs;
        $logs_act = json_decode($schedule['logs_act'],true);
        $logs_act = empty($logs_act)?array():$logs_act;
        array_unshift($logs,$team.' '.$player['name'].' 失误了，直接将球权送给了对方！'."\n".'后场球！');
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

    //更换球员
//    public function replace(){
//        $scheduleId  = $this->request->param('id','','int');
//        $hometeam = $this->request->param('hometeam',1,'int');
//        $team = $hometeam==1?'[主队]':'[客队]';
//        $schedule = Db::name('schedule')->where('Id',$scheduleId)->find();
//        if(empty($schedule))
//            return $this->returnJson('该比赛不存在');
//        if($schedule['acting']!==2)
//            return $this->returnJson('不是死球状态，无法换人');
//        $playerIds = $this->request->param('oldIds','','string');
//        $playerIds = str_replace('，',',',$playerIds);
//        $newPlayerIds = $this->request->param('newIds','','string');
//        $newPlayerIds = str_replace('，',',',$newPlayerIds);
//        $olds = explode(',',$playerIds);
//        $news = explode(',',$newPlayerIds);
//        if(empty($olds) or empty($news))
//            return $this->returnJson('未定义需要更换的球员或更换球员');
//        Db::startTrans();
//        $timeTotal = ($schedule['section'])*$schedule['section_time']-$schedule['second'];
//        $oldsName = $team;
//        foreach ($news as $new){
//            $playerData = Db::name('player_data')->where(['schedule_id'=>$scheduleId,'user_id'=>$new])->find();
//            if(empty($playerData))
//                return $this->returnJson($new.'数据不存在');
//            $update = ['Id'=>$playerData['Id'],'is_playing'=>1,'enter_time'=>$timeTotal];
//            $newRes = Db::name('player_data')->update($update);
//            if(!$newRes){
//                Db::rollback();
//                return $this->returnJson('失败，请重试！');
//            }
//            $name = Db::name('user')->where('Id',$new)->column('name');
//            $name = isset($name[0])?$name[0]:'';
//            $oldsName .= $name.' ';
//        }
//        $oldsName .= '换下 ';
//        foreach ($olds as $old){
//            $playerData = Db::name('player_data')->where(['schedule_id'=>$scheduleId,'user_id'=>$old])->find();
//            if(empty($playerData))
//                return $this->returnJson($old.'数据不存在');
//            $playing_time = $playerData['playing_time']+$timeTotal-$playerData['enter_time'];
//            $update = ['Id'=>$playerData['Id'],'is_playing'=>0,'playing_time'=>$playing_time];
//            $oldRes = Db::name('player_data')->update($update);
//            if(!$oldRes){
//                Db::rollback();
//                return $this->returnJson('失败，请重试！');
//            }
//            $oldname = Db::name('user')->where('Id',$old)->column('name');
//            $oldname = isset($oldname[0])?$oldname[0]:'';
//            $oldsName .= $oldname.' ';
//        }
//        $logs = json_decode($schedule['logs'],true);
//        $logs = empty($logs)?array():$logs;
//        $logs_act = json_decode($schedule['logs_act'],true);
//        $logs_act = empty($logs_act)?array():$logs_act;
//        array_unshift($logs,$oldsName);
//        array_unshift($logs_act,'');
//        $res = Db::name('schedule')->update(['Id'=>$scheduleId,'logs'=>json_encode($logs),'update_time'=>time()]);
//        if(!$res){
//            Db::rollback();
//            return $this->returnJson('失败，请重试！');
//        }
//        Db::commit();
//        return $this->returnJson('成功',true,1);
//    }
    public function replace(){
        $scheduleId  = $this->request->param('id','','int');
        $hometeam = $this->request->param('hometeam',1,'int');
        $team = $hometeam==1?'[主队]':'[客队]';
        $schedule = Db::name('schedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->returnJson('该比赛不存在');
        if($schedule['acting']!==2)
            return $this->returnJson('不是死球状态，无法换人');
        $playerId = $this->request->param('oldId',0,'int');
        $newPlayerId = $this->request->param('newId',0,'int');
        if(empty($olds) or empty($news))
            return $this->returnJson('未定义需要更换的球员或更换球员');
        Db::startTrans();
        $timeTotal = ($schedule['section'])*$schedule['section_time']-$schedule['second'];
        $playerData = Db::name('player_data')->where(['schedule_id'=>$scheduleId,'user_id'=>$newPlayerId])->find();
        if(empty($playerData))
            return $this->returnJson($newPlayerId.'数据不存在');
        $update = ['Id'=>$playerData['Id'],'is_playing'=>1,'enter_time'=>$timeTotal];
        $newRes = Db::name('player_data')->update($update);
        if(!$newRes){
            Db::rollback();
            return $this->returnJson('失败，请重试！');
        }
        $newuser = Db::name('user')->where('Id',$newPlayerId)->find();
        $newname = empty($newuser)?$newuser['name']:'';
        $playerData = Db::name('player_data')->where(['schedule_id'=>$scheduleId,'user_id'=>$playerId])->find();
        if(empty($playerData))
            return $this->returnJson($playerId.'数据不存在');
        $playing_time = $playerData['playing_time']+$timeTotal-$playerData['enter_time'];
        $update = ['Id'=>$playerData['Id'],'is_playing'=>0,'playing_time'=>$playing_time];
        $oldRes = Db::name('player_data')->update($update);
        if(!$oldRes){
            Db::rollback();
            return $this->returnJson('失败，请重试！');
        }
        $olduser = Db::name('user')->where('Id',$playerId)->find();
        $oldname = isset($olduser)?$olduser['name']:'';
        $logs = json_decode($schedule['logs'],true);
        $logs = empty($logs)?array():$logs;
        $logs_act = json_decode($schedule['logs_act'],true);
        $logs_act = empty($logs_act)?array():$logs_act;
        array_unshift($logs,$team.$newname.'换下'.$oldname);
        array_unshift($logs_act,'');
        $res = Db::name('schedule')->update(['Id'=>$scheduleId,'logs'=>json_encode($logs),'update_time'=>time()]);
        if(!$res){
            Db::rollback();
            return $this->returnJson('失败，请重试！');
        }
        Db::commit();
        return $this->returnJson('成功',true,1);
    }


    //开始或暂停
    public function stop(){
        $scheduleId = $this->request->param('id',0,'int');
        $type = (int)$this->request->param('type',0,'int');
        $second = (int)$this->request->param('second',0,'int');
        $hometeam = (int)$this->request->param('hometeam',0,'int');
        $team = $hometeam==1?'[主队]':'[客队]';
        $schedule = Db::name('schedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->returnJson('比赛不存在');
        if(!in_array($schedule['acting'],[1,2]))
            return $this->returnJson('参数错误');
        $stopStatus = $schedule['acting'] == 1?2:1;
        if( $schedule['acting']==0){
            $stopStr = '比赛开始';
        }elseif( $schedule['acting']== 1){
            $stopStr = $type==1?($team.'比赛暂停'):'死球暂停';
        }else{
            $stopStr = '比赛继续';
        }
        $logs = json_decode($schedule['logs'],true);
        $logs = empty($logs)?array():$logs;
        array_unshift($logs,$stopStr);
        $logs_act = json_decode($schedule['logs_act'],true);
        $logs_act = empty($logs_act)?array():$logs_act;
        $update = ['acting'=>$stopStatus,'Id'=>$scheduleId,'update_time'=>time(),'logs'=>json_encode($logs),'second'=>$second];
        if($type == 1){
            $stopKey = $hometeam==1?'home':'visting';
            $stopKey .= $schedule['section']>2?'_fhalf_stop':'_shalf_stop';
            array_unshift($logs_act,[$stopKey=>'stop']);
            if($schedule[$stopKey]==0)
                return $this->returnJson('暂停次数已用完');
            $update[$stopKey] = $schedule[$stopKey]-1;
        }else{
            array_unshift($logs_act,'');
        }
        $update['logs_act']=json_encode($logs_act);
        $res = Db::name('schedule')->update($update);
        if(!$res)
            return $this->returnJson('失败，请重试!');
        return $this->returnJson('成功',true,1);
    }
    //结束比赛
    public function over(){
        $scheduleId = $this->request->param('id',0,'int');
        $clear = $this->request->param('clear',0,'int');
        $schedule = Db::name('schedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->returnJson('比赛不存在');
        if($schedule['section']<4 or $schedule['second']!=0)
            return $this->returnJson('比赛时间或节数未结束');
        $update = ['acting'=>3,'Id'=>$scheduleId,'update'=>time()];
        $logs = json_decode($schedule['logs'],true);
        $logs = empty($logs)?array():$logs;
        array_unshift($logs,'比赛结束');
        $update['logs'] = json_encode($logs);
        if($clear==1)
            $update['logs_act'] = '';
        $res = Db::name('schedule')->update($update);
        if(!$res)
            return $this->returnJson('失败，请重试!');
        return $this->returnJson('成功',true,1);
    }

    //撤销
    public function returnBack(){
        $scheduleId = $this->request->param('id',0,'int');
        $schedule = Db::name('schedule')->where('Id',$scheduleId)->find();
        if(empty($schedule))
            return $this->returnJson('比赛不存在');
        if($schedule['acting']==3)
            return $this->returnJson('比赛已结束，无法撤回');
        $actLogs = json_decode($schedule['logs_act'],true);
        $logs = json_decode($schedule['logs'],true);
        $unsetLogs = array_shift($actLogs);
        Db::startTrans();
        $stop = '';
        foreach ($unsetLogs as $userId=>$log){
            if($log=='stop'){
                $stop = $userId;
                continue;
            }
            $res = $this->doBack($log,$userId,$scheduleId);
            if(!$res){
                Db::rollback();
                return $this->returnJson('失败，请重试!');
            }
        }
        array_shift($logs);
        $update = ['Id'=>$scheduleId,'logs_act'=>json_encode($actLogs),'logs'=>json_encode($logs)];
        if($stop)
            $update[$stop] = $schedule[$stop]+1;
        $logRes = Db::name('player_data')->update($update);
        if(!$logRes){
            Db::rollback();
            return $this->returnJson('失败，请重试！');
        }
        Db::commit();
        return $this->returnJson('成功',true,1);
    }

    //撤销执行
    public function doBack($log,$userId,$scheduleId){
        $num = 1;
        switch ($log){
            case 'three_hit':
               $num = 3;
                break ;
            case 'hit':
                $num = 2;
                break ;
            default:
                $num = 1;
                break;
        }
        $playerData = Db::name('player_data')->where(['schedule_id'=>$scheduleId,'user_id'=>$userId])->find();
        if(empty($playerData))
            return false;
        $update = ['Id'=>$playerData['Id'],$log=>$playerData[$log]-$num];
        return Db::name('player_data')->update($update);
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
