<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/4/3
 * Time: 20:20
 */
namespace app\user\controller;
use base\Userbase;
use think\Db;
use ku\Gameplan;
use think\Loader;
use think\Cache;

class Schedule extends Userbase{
    private $_excles = array('xls','xlsx','xlsb',);
    private $_scheduleClu = array('home_team'=>'主队','visiting_team'=>'客队','game_time'=>'比赛时间','game_address'=>'比赛场地');

    public function index(){

    }

    public function order(){
        $id = $this->request->param('id','','int');
        $user = $this->getUser();
        $event = Db::name('event')->where(['Id'=>$id,'create_user'=>$user['Id']])->find();
        if(empty($event))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'赛事不存在或您没有该赛事的权限']);
        $joins = json_decode($event['join_clubs'],true);

    }

    public function group(){
        $id= $this->request->param('id','','int');
        $cache = (int)$this->request->param('cache',0,'int');
        $event = Db::name('event')->where('Id',$id)->find();
        if(empty($event))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'赛事不存在']);
        $this->assign('event',$event);
        if(strtotime($event['start_time'])<strtotime('Y-m-d'))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'赛事已开始，如需操作请重新设置比赛时间','url'=>'/user/event/management?id='.$event['Id']]);
        $joins = json_decode($event['join_clubs'],true);
        $joinsId = array_keys($joins);
        $dataStr = Cache::get('schedule.system.group.str.'.$event['Id']);
        if($dataStr === false or $cache===0){
            if($event['promotion_num'] >= count($joins))
                return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'晋级队伍大于或等于原队伍数，若要坚持此方案请更改比赛类型','url'=>'/user/event/management?id='.$event['Id']]);
            $dayTimes= (int)$this->request->param('dayTimes','','int');
            $groupWith= (int)$this->request->param('groupWith',1,'int');
            $freeTime= (float)$this->request->param('freeTime','','string');
            $bTime= $this->request->param('bTime','','string');
            $bTime = explode(':',$bTime);
            if(count($bTime)!=2 or $bTime[0]>24 or $bTime[0]<0 or $bTime[1]>60 or $bTime[1]<0)
                return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'设置比赛开始时间有误','url'=>'/user/event/management?id='.$event['Id']]);
            $num = (int)$this->request->param('num','','int');
            $divideTime = (int)$this->request->param('divideTime','','int');
            $plan = new Gameplan();
            $gameTime = strtotime($event['start_time']);
            $config = [
                'promotion'=>$event['promotion_num'],'dayTimes'=>$dayTimes,'num'=>$num,
            ];
            $schedules = $plan->groupGame($joinsId,$config);
            $groupAsName = range('A','Z');
            $showSchedules = [];
            if($divideTime ==30)
                $timeStr = '1 month';
            else
                $timeStr = $divideTime.' day';
            $first=0;
            foreach ($schedules as $groupKey=>$schedule){
                $first = $groupKey;
                if(empty($schedule))
                    return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'赛程设置有误，请重新设置正确的赛程方案']);
                foreach ($schedule as $key=>$oneSchedule){
                    $gameTime = date('Y-m-d',$gameTime);
                    $gameTime = strtotime('+'.$bTime[0].' hour',strtotime($gameTime));
                    $gameTime = strtotime('+'.$bTime[1].' minute',$gameTime);
                    if($key !=0 or $groupKey != 0){
                        $gameTime = strtotime('+ '.$timeStr,$gameTime);
                    }
                    foreach ($oneSchedule as $k=>$battles){
                        if($k!=0){
                            $tempTime = explode('.',$freeTime);
                            $gameTime = strtotime('+ '.$tempTime[0].'hour',$gameTime);
                            $minute = isset($tempTime[1])?(float)'0'.$tempTime[1] * 60:0;
                            $gameTime = strtotime('+ '.$minute.' minute',$gameTime);
                        }
                        $showSchedule = $battles;
                        $showSchedule['game_time'] = $gameTime;
                        $oneSchedule[$k] = $showSchedule;
                    }
                    $showSchedules[$groupAsName[$groupKey]][$key] = $oneSchedule;
                }
                break;
            }
            $count = count($schedules);
            for($i=$first+1;$i<$count;$i++){
                foreach ($schedules[$i] as $key=>$oneSchedule){
                    foreach ($oneSchedule as $k=>$battles){
                        $showSchedule = $battles;
                        if($groupWith===1){
                            $showSchedule['game_time'] = $showSchedules[$groupAsName[$i-1]][$key][$k]['game_time'];
                        }else{
                            $time = $showSchedules[$groupAsName[$i-1]][$key][$k]['game_time'];
                            $tempTime = explode('.',$freeTime);
                            $time = strtotime('+ '.$tempTime[0].'hour',$time);
                            $minute = isset($tempTime[1])?(float)'0'.$tempTime[1] * 60:0;
                            $time = strtotime('+ '.$minute.' minute',$time);
//                            $showSchedule['game_time'] = strtotime('+'.$freeTime.' hour',$showSchedules[$groupAsName[$i-1]][$key][$k]['game_time']);
                            $showSchedule['game_time'] = $time;
                        }
                        $oneSchedule[$k] = $showSchedule;
                    }
                    $showSchedules[$groupAsName[$i]][$key] = $oneSchedule;
                }
            }
        }else{
            $showSchedules = json_decode($dataStr,true);
        }
        $this->assign('joins',$joins);
        Cache::set('schedule.system.group.str.'.$event['Id'],json_encode($showSchedules),1200);
        $this->assign('schedules',$showSchedules);
        return $this->fetch();
    }

    public function import(){
        $request = $this->request;
        $id = $request->param('id',0,'int');
        $event = Db::name('event')->where('Id',$id)->find();
        if(empty($event))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'赛事不存在']);
        $user = $this->getUser();
        if($user['Id']!==$event['create_user'])
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'您没有操作权限']);
        $dataStr = Cache::get('schedule.data.str.'.$id);
        $file = $request ->file('importSchedules');
        if(empty($file) and $dataStr===false)
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'未上传文件']);
        if($dataStr===false or !empty($file)){
            $fileInfo = $file->getInfo();
            if (empty($fileInfo))
                return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'没有文件上传！']);
            $name=explode('.',$fileInfo['name']);
            $lastName=$name[count($name)-1];
            if(!in_array(strtolower($lastName),$this->_excles))
                return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'上传文件格式必须为'.implode(',',$this->_excles)]);
            if ($fileInfo['error'] > 0)
                return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'上传错误！']);
            $basePath = PUBLIC_PATH.'../extend/excel/';
            Loader::import('PHPExcel',$basePath);
            Loader::import('PHPExcel/IOFactory.PHPExcel_IOFactory',$basePath);
            $read = \PHPExcel_IOFactory::createReader('Excel2007');
            $obj = $read->load($fileInfo['tmp_name']);
            $dataArray =$obj->getActiveSheet()->toArray();
            foreach ($dataArray as $key=> $item){
                $dataArray[$key] = array_filter($item);
            }
            $datas = array_filter($dataArray);
            $virefy = [];
            if($event['type']===0)
                $this->_scheduleClu['group']= '小组';
            foreach ($this->_scheduleClu as $key=>$clu){
                $cluKey =  array_search($clu,$datas[0]);
                if($cluKey === false){
                    return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'导入表格不包含'.$clu.'的数据']);
                }
                $virefy[$key] = $cluKey;
            }
            unset($datas[0]);
            $schedules = [];
            foreach ($datas as $key=>$data){
                foreach ($virefy as $k=>$v){
                    $schedules[$key][$k]=isset($data[$v])?$data[$v]:'';
                }
                $schedules[$key]['game_time'] = strtotime($schedules[$key]['game_time']);
            }
        }else{
            $schedules = json_decode($dataStr,true);
        }
        $this->assign('event',$event);
        $this->assign('schedules',$schedules);
        $dataStr = empty($file)?$dataStr:json_encode($schedules);
//        $this->assign('dataStr',urlencode($dataStr));
        Cache::set('schedule.data.str.'.$id,$dataStr,1200);//缓存20分钟
        return $this->fetch();
    }

    public function importData(){
        $eventId =  $this->request->param('id',0,'int');
        $event = Db::name('event')->where('Id',$eventId)->find();
        $firstStop = $this->request->param('firstStop',0,'int');
        $lastStop = $this->request->param('lastStop',0,'int');
        if(empty($firstStop) and empty($lastStop))
            return $this->returnJson('暂停次数为零');
        $sectionTime = $this->request->param('sectionTime',10,'int');
        $sectionTime *= 60;
//        $dataStr = $this->request->param('dataStr','','string');
        $dataStr = Cache::get('schedule.data.str.'.$eventId);
        if($dataStr === false)
            return $this->returnJson('获取数据错误，请重新导入');
//        $import = json_decode(urldecode($dataStr),true);
        $import = json_decode($dataStr,true);
        if(!$import)
            return $this->returnJson('数据错误');
        $add = ['event_id'=>$eventId,'second'=>$sectionTime,'section_time'=>$sectionTime,'home_fhalf_stop'=>$firstStop,
            'visiting_fhalf_stop'=>$firstStop,'home_shalf_stop'=>$lastStop,'visiting_shalf_stop'=>$lastStop,
            ];
        Db::startTrans();
        $clubModel = Db::name('club');
        $joinClubs = json_decode($event['join_clubs'],true);
        foreach ($import as $key=>$schedule){
            if($schedule['game_time']<time() or $schedule['game_time']<strtotime($event['start_time'].' 00:00:00') or $schedule['game_time']>strtotime($event['end_time'].' 23:59:59'))
                return $this->returnJson('比赛时间必须大于当前时间，并且再比赛时间范围内');
            $home = $clubModel->where('name',trim($schedule['home_team']))->find();
            if(empty($home)){
                Db::rollback();
                return $this->returnJson($schedule['home_team'].'不存在');
            }
            if(!isset($joinClubs[$home['Id']]))
                return $this->returnJson($schedule['home_team'].'不在比赛队列中');
            $visiting = $clubModel->where('name',trim($schedule['visiting_team']))->find();
            if(empty($visiting)){
                Db::rollback();
                return $this->returnJson($schedule['visiting_team'].'不存在');
            }
            if(!isset($joinClubs[$visiting['Id']]))
                return $this->returnJson($schedule['visiting_team'].'不在比赛队列中');
            $add['home_team'] = $home['Id'];
            $add['visiting_team'] = $visiting['Id'];
            $add['game_time'] = $schedule['game_time'];
            $add['game_address'] = $schedule['game_address'];
            if($event['type']===0){
                $add['group'] = $schedule['group'];
            }
            $res = Db::name('schedule')->insert($add);
            if(!$res){
                Db::rollback();
                return $this->returnJson('操作失败，请重试');
            }
        }
        $update = ['Id'=>$eventId,'status'=>1];
        $res = Db::name('event')->update($update);
        if(!$res){
            Db::rollback();
            return $this->returnJson('操作失败，请重试');
        }
        if($event['type']===0)
            $formatModel = Db::name('group_format');
        elseif ($event['type']===1)
            $formatModel = Db::name('order_format');
        else
            $formatModel = Db::name('out_format');
        $formatAdd = ['event_id'=>$eventId];
        foreach ($joinClubs as $key =>$joinClub){
            $formatAdd['club_id'] = $key;
            if($event['type'] === 0){
                $Myschedule = Db::name('schedule')->where('home_team|visiting_team',$key)->find();
                $formatAdd['group'] = $Myschedule['group'];
            }
            $addRes = $formatModel->insert($formatAdd);
            if(!$addRes){
                Db::rollback();
                return $this->returnJson('操作失败，请重试');
            }
        }
        Db::commit();
        Cache::rm('schedule.data.str.'.$eventId);
        return $this->returnJson('赛程安排成功',true,1);
    }

    public function groupData(){
        $eventId =  $this->request->param('id',0,'int');
        $event = Db::name('event')->where('Id',$eventId)->find();
        $firstStop = $this->request->param('firstStop',0,'int');
        $lastStop = $this->request->param('lastStop',0,'int');
        $game_address = $this->request->param('game_address','','string');
        if(empty($game_address))
            return $this->returnJson('比赛地点未设置');
        if(empty($firstStop) and empty($lastStop))
            return $this->returnJson('暂停次数为零');
        $sectionTime = $this->request->param('sectionTime',10,'int');
        $sectionTime *= 60;
        $dataStr = Cache::get('schedule.system.group.str.'.$eventId);
        if($dataStr === false)
            return $this->returnJson('获取数据错误，请重新导入');
        $import = json_decode($dataStr,true);
        if(!$import)
            return $this->returnJson('数据错误');
        $add = ['event_id'=>$eventId,'second'=>$sectionTime,'section_time'=>$sectionTime,'home_fhalf_stop'=>$firstStop,
            'visiting_fhalf_stop'=>$firstStop,'home_shalf_stop'=>$lastStop,'visiting_shalf_stop'=>$lastStop,
        ];
        Db::startTrans();
        $clubModel = Db::name('club');
        $joinClubs = json_decode($event['join_clubs'],true);
        foreach ($import as $key=>$schedules){
            foreach ($schedules as $k=>$value){
                foreach ($value as $schedule){
                    if($schedule['game_time']<time() or $schedule['game_time']<strtotime($event['start_time'].' 00:00:00') or $schedule['game_time']>strtotime($event['end_time'].' 23:59:59'))
                        return $this->returnJson('比赛时间必须大于当前时间，并且再比赛时间范围内');
                    $home = $clubModel->where('Id',trim($schedule['home_team']))->find();
                    if(empty($home)){
                        Db::rollback();
                        return $this->returnJson($schedule['home_team'].'不存在');
                    }
                    if(!isset($joinClubs[$home['Id']]))
                        return $this->returnJson($schedule['home_team'].'不在比赛队列中');
                    $visiting = $clubModel->where('Id',trim($schedule['visiting_team']))->find();
                    if(empty($visiting)){
                        Db::rollback();
                        return $this->returnJson($schedule['visiting_team'].'不存在');
                    }
                    if(!isset($joinClubs[$visiting['Id']]))
                        return $this->returnJson($schedule['visiting_team'].'不在比赛队列中');
                    $add['home_team'] = $home['Id'];
                    $add['visiting_team'] = $visiting['Id'];
                    $add['game_time'] = $schedule['game_time'];
                    $add['game_address'] = $game_address;
                    $add['group'] = $key;
                    $res = Db::name('schedule')->insert($add);
                    if(!$res){
                        Db::rollback();
                        return $this->returnJson('操作失败，请重试');
                    }
                }
            }
        }
        $update = ['Id'=>$eventId,'status'=>1];
        $res = Db::name('event')->update($update);
        if(!$res){
            Db::rollback();
            return $this->returnJson('操作失败，请重试');
        }
        if($event['type']===0)
            $formatModel = Db::name('group_format');
        elseif ($event['type']===1)
            $formatModel = Db::name('order_format');
        else
            $formatModel = Db::name('out_format');
        $formatAdd = ['event_id'=>$eventId];
        foreach ($joinClubs as $key =>$joinClub){
            $formatAdd['club_id'] = $key;
            if($event['type'] === 0){
                $Myschedule = Db::name('schedule')->where('home_team|visiting_team',$key)->find();
                $formatAdd['group'] = $Myschedule['group'];
            }
            $addRes = $formatModel->insert($formatAdd);
            if(!$addRes){
                Db::rollback();
                return $this->returnJson('操作失败，请重试');
            }
        }
        Db::commit();
        Cache::rm('schedule.system.group.str.'.$eventId);
        return $this->returnJson('赛程安排成功',true,1);
    }

    //下载模板
    public function downTemp(){
        header('Content-Type:application/xlsx');
        header('Content-Disposition:attachment;filename=schedule.xlsx');
        header('Cache-Control:max-age=0');
        readfile(PUBLIC_PATH.'/data/schedule_temp.xlsx');
        exit();
    }

    //获取导入赛程数据
    public function getAltSchedule(){
        $eventId = $this->request->param('eventId',0,'int');
        $id= (int)$this->request->param('id',-1,'int');
        if(!is_int($id))
            return $this->returnJson('参数错误');
        $event = Db::name('event')->where('Id',$eventId)->find();
        if(empty($event))
            return $this->returnJson('赛事不存在');
        $dataStr = Cache::get('schedule.data.str.'.$eventId);
        $data = json_decode($dataStr,true);
        if(!isset($data[$id]))
            return $this->returnJson('修改的赛程不存在');
        return $this->returnJson('获取成功',true,1,$data[$id]);
    }
    //获取分组赛程数据
    public function getAltGroup(){
        $eventId = $this->request->param('eventId',0,'int');
        $key= $this->request->param('key','-1','string');
        $k= $this->request->param('k',-1,'int');
        $my= $this->request->param('my',-1,'int');
//        if(!is_int($k))
//            return $this->returnJson('参数错误');
        $event = Db::name('event')->where('Id',$eventId)->find();
        if(empty($event))
            return $this->returnJson('赛事不存在');
        $dataStr = Cache::get('schedule.system.group.str.'.$eventId);
        $data = json_decode($dataStr,true);
        if(!isset($data[$key][$k][$my]))
            return $this->returnJson('修改的赛程不存在');
        return $this->returnJson('获取成功',true,1,$data[$key][$k][$my]);
    }

    //修改导入赛程
    public function altSchedule(){
        $eventId = $this->request->param('eventId',0,'int');
        $id= (int)$this->request->param('id',-1,'string');
        if(!is_int($id))
            return $this->returnJson('参数错误');
        $event = Db::name('event')->where('Id',$eventId)->find();
        if(empty($event))
            return $this->returnJson('赛事不存在');
        $dataStr = Cache::get('schedule.data.str.'.$eventId);
        $data = json_decode($dataStr,true);
        if(!isset($data[$id]))
            return $this->returnJson('修改的赛程不存在');
        $gameTime= $this->request->param('game_time','','string');
        $game_address= $this->request->param('game_address','','string');
        $gameTime = strtotime($gameTime);
        if($gameTime<time() or $gameTime<strtotime($event['start_time'].' 00:00:00') or $gameTime>strtotime($event['end_time'].' 23:59:59'))
            return $this->returnJson('比赛时间必须大于当前时间，并且再比赛时间范围内');
        $data[$id]['game_time'] = $gameTime;
        $data[$id]['game_address'] = $game_address;
        Cache::set('schedule.data.str.'.$eventId,json_encode($data),1200);
        return $this->returnJson('修改成功',true,1);
    }
 //修改分组赛程
    public function altGroup(){
        $eventId = $this->request->param('eventId',0,'int');
        $key= $this->request->param('key','-1','string');
        $k= $this->request->param('k',-1,'int');
        $my= $this->request->param('my',-1,'int');
        $event = Db::name('event')->where('Id',$eventId)->find();
        if(empty($event))
            return $this->returnJson('赛事不存在');
        $dataStr = Cache::get('schedule.system.group.str.'.$eventId);
        $data = json_decode($dataStr,true);
        if(!isset($data[$key][$k][$my]))
            return $this->returnJson('修改的赛程不存在');
        $gameTime= $this->request->param('game_time','','string');
        $gameTime = strtotime($gameTime);
        if($gameTime<time() or $gameTime<strtotime($event['start_time'].' 00:00:00') or $gameTime>strtotime($event['end_time'].' 23:59:59'))
            return $this->returnJson('比赛时间必须大于当前时间，并且再比赛时间范围内');
        $data[$key][$k][$my]['game_time'] = $gameTime;
        Cache::set('schedule.system.group.str.'.$eventId,json_encode($data),1200);
        return $this->returnJson('修改成功',true,1);
    }



}