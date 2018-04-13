<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/4/1
 * Time: 10:51
 */
namespace app\user\controller;
use base\Userbase;
use think\Db;
use ku\Email;

class Club extends Userbase{

    public function index(){
        $id= $this->request->param('id','','int');
        $user =$this->getUser();
        $myClub = Db::name('club')->where('Id',$id)->find();
        if(empty($myClub))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'球队不存在']);
        $players = json_decode($myClub['players'],true);
        if(!isset($players[$user['Id']]))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'您未加入该球队']);
        $this->assign('club',$myClub);
        $this->assign('players',$players);
        $applys = Db::name('club_apply')->where(['club_id'=>$id])->select();
        $this->assign('applys',$applys);
        return $this->fetch();
    }

    public  function player(){
        $Id= $this->request->param('id','','int');
        $user = Db::name('user')->where('Id',$Id)->find();
        if(empty($user))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'没有该球员']);
        $this->assign('user',$user);
        return $this->fetch();
    }

    //加入赛事比赛
    public function joinEvent(){
        $id = $this->request->param('id','','int');
        $clubId = $this->request->param('clubId','','int');
        $code = $this->request->param('code','','string');
        $event = Db::name('event')->where('Id',$id)->find();
        if(empty($event))
            return $this->returnJson('赛事不存在');
        $user = $this->getUser();
        $club = Db::name('club')->where('Id',$clubId)->find();
        if(empty($club))
            return $this->returnJson('没有该球队');
        if($club['captain'] != $user['Id'])
            return $this->returnJson('您不是队长，没有权限加入比赛');
        if(strcmp($code,$event['virefy_code'])!==0)
            return $this->returnJson('邀请码错误，请重新确认');
//        $playerNum = count(json_decode($club['join_clubs'],true));
//        if($playerNum<7)
//            return $this->returnJson('球队人数小于7人无法参加比赛');
        $joins = json_decode($event['join_clubs'],true);
        if(isset($joins[$clubId]))
            return $this->returnJson('已经加入比赛，无需重复操作');
        $joins[$clubId] = $club['name'];
        $logs = json_decode($club['log'],true);
        $log = date('Y-m-d H:i:s').' '.$user['name'].'设置球队加入“'.$event['name'].'”的比赛';
        array_unshift($logs,$log);
        $update = ['Id'=>$event['Id'],'join_clubs'=>json_encode($joins)];
        Db::startTrans();
        $res = Db::name('event')->update($update);
        if(!$res)
            return $this->returnJson('加入比赛失败，请重试');
        $res = Db::name('club')->update(['Id'=>$clubId,'log'=>json_encode($logs)]);
        if(!$res){
            Db::rollback();
            return $this->returnJson('写入球队日志错误，请重试');
        }
        Db::commit();
        return $this->returnJson('加入比赛成功',true,1);

    }

    //获取队长可参加该比赛的队伍
    public function captainClub(){
        $eventId = $this->request->param('eventId','','int');
        $event = DB::name('event')->where('Id',$eventId)->find();
        if(empty($event))
            return $this->returnJson('赛事不存在');
        $joins = json_decode($event['join_clubs'],true);
        $clubIds = empty($joins)?array():array_keys($joins);
        $user = $this->getUser();
        $clubs = Db::name('club')
            ->where('captain',$user['Id'])
            ->where('Id','not in',$clubIds)
            ->select();
        if(empty($clubs))
            return $this->returnJson("没有可加入的队伍");
        return $this->returnJson('获取成功',true,1,$clubs);
    }

    //更换队长
    public function changeCaptain(){
        $user = $this->getUser();
        $id = $this->request->param('id','','int');
        $playerId = $this->request->param('playerId','','int');
        $player = Db::name('user')->where('Id',$playerId)->find();
        if(empty($player))
            return $this->returnJson('用户不存在');
        $club = Db::table('club')->where('Id',$id)->find();
        if (empty($club))
            return $this->returnJson('球队不存在');
        if($user['Id'] != $club['captain'])
            return $this->returnJson('您不是队长，无法操作');
        if($playerId==$club['captain'])
            return $this->returnJson('不能将队长转给自己');
        $players = json_decode($club['players'],true);
        if(!isset($players[$playerId]))
            return $this->returnJson('该用户不在球队');
        $logs = json_decode($club['log'],true);
        $log = date('Y-m-d H:i:s').' '.$user['name'].'将队长转让给'.$player['name'];
        array_unshift($logs,$log);
        $update = ['Id'=>$id,'captain'=>$playerId,'log'=>json_encode($logs)];
        $res = Db::name('club')->update($update);
        if(!$res)
            return $this->returnJson('操作失败，请重试');
        return $this->returnJson('更换成功',true,1);
    }

    public function dealApply(){
        $user =$this->getUser();
        $id = $this->request->param('id','','int');
        $deal = (int)$this->request->param('deal','','int');
        if($deal!= 1 and $deal !=0)
            return $this->returnJson('处理参数不正确');
        $apply = Db::name('club_apply')->where('Id',$id)->find();
        if(empty($apply))
            return $this->returnJson('申请列表不存在');
        $club = Db::name('club')->where('Id',$apply['club_id'])->find();
        if(empty($club))
            return $this->returnJson('球队不存在');
        if($user['Id']!= $club['captain'])
            return $this->returnJson('您不是队长，无法处理申请');
        $subject = '来战吧篮球通知';
        $applyUser = Db::name('user')->where('Id',$apply['user_id'])->find();
        if(empty($applyUser)){
            Db::name('club_apply')->where('Id',$id)->delete();
            return $this->returnJson('申请用户存在');
        }
        Db::startTrans();
        if($deal === 0){
            $body = '您申请加入"'.$club['name'].'"拒绝了你的申请';
            $logs = json_decode($club['log'],true);
            $log = date('Y-m-d H:i:s').' '.$user['name'].'拒绝了'.$applyUser['name'].' 加入球队';
            array_unshift($logs,$log);
            $update= ['Id'=>$club['Id'],'log'=>json_encode($logs)];
            $res = Db::name('club')->update($update);
            if(!$res)
                return $this->returnJson('操作失败，请重试!');
        }
        else{
            $body = '您申请加入"'.$club['name'].'"通过了你的申请';
            $players = json_decode($club['players'],true);
            if(!isset($players[$applyUser['Id']])){
                $players[$applyUser['Id']] = $applyUser['name'];
                $logs = json_decode($club['log'],true);
                $log = date('Y-m-d H:i:s').' '.$user['name'].'同意了'.$applyUser['name'].' 加入球队';
                array_unshift($logs,$log);
                $update= ['Id'=>$club['Id'],'players'=>json_encode($players),'log'=>json_encode($logs)];
                $res = Db::name('club')->update($update);
                if(!$res)
                    return $this->returnJson('操作失败，请重试!');
            }
        }
        Email::sendEmail($applyUser['email'],$subject,$body);
        $res = Db::name('club_apply')->where('Id',$id)->delete();
        if(!$res){
            Db::rollback();
            return $this->returnJson('操作失败，请重试!');
        }
        Db::commit();
        return $this->returnJson('处理成功',true,1);
    }

    public function delplayer(){
        $user =$this->getUser();
        $id = $this->request->param('id','','int');
        $playerId = $this->request->param('playerId','','int');
        $player = Db::name('user')->where('Id',$playerId)->find();
        if(empty($player))
            return $this->returnJson('用户不存在');
        $club = Db::table('club')->where('Id',$id)->find();
        if (empty($club))
            return $this->returnJson('球队不存在');
        if($user['Id'] != $club['captain'])
            return $this->returnJson('您不是队长，无法操作');
        if($playerId==$club['captain'])
            return $this->returnJson('不能自己踢出队伍');
        $players = json_decode($club['players'],true);
        if(!isset($players[$playerId]))
            return $this->returnJson('该用户不在球队');
        unset($players[$playerId]);
        $logs = json_decode($club['log'],true);
        $log = date('Y-m-d H:i:s').' '.$user['name'].'将'.$player['name'].'踢出队伍';
        array_unshift($logs,$log);
        $update = ['Id'=>$id,'players'=>json_encode($players),'log'=>json_encode($logs)];
        $res = Db::name('club')->update($update);
        if(!$res)
            return $this->returnJson('操作失败，请重试');
        return $this->returnJson('踢出成功',true,1);
    }

    public function applyEvent(){
        $eventId = $this->request->param('id',0,'int');
        $event = Db::name('event')->where('Id',$eventId)->find();
        if(empty($event))
            return $this->returnJson('赛事不存在');
        if(strtotime($event['start_time'])<time())
            return $this->returnJson('赛事已开始,无法报名');
        $clubId = $this->request->param('clubId',0,'int');
        $club = Db::name('club')->where('Id',$clubId)->find();
        if(empty($club))
            return $this->returnJson('球队不存在');
        $user = $this->getUser();
        if($club['captain']!=$user['Id'])
            return $this->returnJson('您不是队长，没有操作权限');
        $reason = $this->request->param('reason','','string');
        $exist = Db::name('event_apply')->where(['club_id'=>$clubId,'event_id'=>$eventId])->find();
        if(!empty($exist))
            return $this->returnJson('已申请，请勿重复操作');
        $add = ['club_id'=>$clubId,'event_id'=>$eventId,'reason'=>$reason,'time'=>time()];
        $logs = json_decode($club['log'],true);
        $log = date('Y-m-d H:i:s').' '.$user['name'].'申请加入'.$event['name'].'比赛';
        array_unshift($logs,$log);
        $update = ['Id'=>$clubId,'log'=>json_encode($logs)];
        Db::startTrans();
        $res = Db::name('event_apply')->insert($add);
        if(!$res)
            return $this->returnJson('申请失败，请重试');
        $res = Db::name('club')->update($update);
        if(!$res){
            Db::rollback();
            return $this->returnJson('更新球队日志失败');
        }
        Db::commit();
        return $this->returnJson('申请成功',true,1);
    }

}