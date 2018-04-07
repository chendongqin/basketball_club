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

class Schedule extends Userbase{

    public function index(){
        $test = $this->testData(2);
        $plan = new Gameplan();
        $data = $plan->groupGame($test,['promotion'=>1,'dayTimes'=>3]);
        return json($data);
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
        $event = Db::name('event')->where('Id',$id)->find();
        if(empty($event))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'赛事不存在']);
        if(strtotime($event['start_time'])<time())
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'赛事已开始，如需操作请重新设置比赛时间','url'=>'/user/event/management?id='.$event['Id']]);
        $joins = json_decode($event['join_clubs'],true);
        $joinsId = array_keys($joins);
        $promotion= (int)$this->request->param('promotion','','int');
        if($promotion == count($joins))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'晋级队伍与原队伍相同，若要坚持此方案请更改比赛类型','url'=>'/user/event/management?id='.$event['Id']]);
        $dayTimes= (int)$this->request->param('dayTimes','','int');
        $num = (int)$this->request->param('num','','int');
        $divideTime = $this->request->param('divideTime','','int');
        if($divideTime ==30)
            $timeStr = '1 month';
        else
            $timeStr = $divideTime.' day';
        $plan = new Gameplan();
        $gameTime = strtotime($event['start_time']);
        $schedules = $plan->groupGame($joinsId,['promotion'=>$promotion,'dayTimes'=>$dayTimes,'num'=>$num]);
        $groupAsName = range('A','Z');
        $showSchedules = [];
        foreach ($schedules as $groupKey=>$schedule){
            if(empty($schedule))
                return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'赛程设置有误，请重新设置正确的赛程方案']);
            foreach ($schedule as $key=>$oneSchedule){
                if($key !=0)
                    $gameTime = strtotime('+ '.$timeStr,$gameTime);
                foreach ($oneSchedule as $battles){
                    $showSchedules[$groupAsName[$groupKey]] = $battles;
                    $showSchedules[$groupAsName[$groupKey]]['game_time'] = $gameTime;
                }
            }
        }
        $this->assign('joins',$joins);
        $this->assign('jsonSchedules',json_encode($showSchedules));
        $this->assign('schedules',$showSchedules);
        return $this->fetch();
    }

    public function testData($num = 4){
        return range(1,$num);
    }


}