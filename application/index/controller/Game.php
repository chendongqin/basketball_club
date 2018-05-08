<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/5/5
 * Time: 11:04
 */
namespace app\index\controller;
use base\Base;
use think\Cache;

class Game extends Base{

    public function index(){
        $scheduleId = $this->request->param('id',0,'int');
        $schedule = Db::name('schedule')->where('Id',$scheduleId);
        if(empty($schedule))
            return $this->returnJson('比赛不存在');
        if($schedule['acting'] === 0)
            return $this->returnJson('比赛未开始');
        $homeData = Db::name('player_data')
            ->where(['schedule_id'=>$scheduleId,'club_id'=>$schedule['home_team']])
            ->select();
        $this->assign('homeData',$homeData);
        $visitingData = Db::name('player_data')
            ->where(['schedule_id'=>$scheduleId,'club_id'=>$schedule['visiting_team']])
            ->select();
        $this->assign('visitingData',$visitingData);


    }


}