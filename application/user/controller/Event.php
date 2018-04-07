<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/7
 * Time: 22:18
 */
namespace  app\user\controller;
use base\Userbase;
use think\Db;
use think\Session;
use ku\Upload;
use ku\Tool;
use think\Cache;
use think\Config;
class Event extends Userbase{

//    protected $_eventTypes = array(0=>'小组赛(常规赛)',1=>'排名赛(季后赛)',2=>'淘汰赛');

    public function index(){
        $this->assign('title','我的赛事管理');
        $page = $this->request->param('page',1,'int');
        $user  = $this->getUser();
        $eventModel = Db::name('event');
        $where[ 'create_user'] = $user['Id'];
        $where['audit'] = 1;
        $types = Config::get('basketball.event_types');
        $this->assign('types',$types);
        $events = $eventModel->where($where)
            ->order('create_time desc')
            ->paginate(10,false,array('page'=>$page))
            ->toArray();
        $this->assign('pager',$events);
        return $this->fetch();
    }


    //添加赛事
    public function add(){
        $this->assign('title','添加赛事');
        $eventTypes = Config::get('basketball.event_types');
        $this->assign('types',$eventTypes);
        $provinces = Db::name('provinces')->select();
        $proData = array();
        foreach ($provinces as $item){
            $proData[$item['provinceid']] = $item['province'];
        }
        $this->assign('provinces',$proData);
        return $this->fetch();
    }

    //执行添加
    public function actadd(){
        $request = $this->request;
        $data = array();
        $user = $this->getUser();
        if ($user['certification'] !=1)
            return $this->returnJson('身份未认证');
        $data['name'] = $request->param('name','','string');
        $data['type'] = (int)$request->param('type','','int');
        $data['address'] = $request->param('address','','string');
        $data['posters'] = $request->param('posters','','string');
        $data['start_time'] = strtotime($request->param('startTime','','string'));
        $data['end_time'] = strtotime($request->param('endTime','','string'));
        if($data['type'] === 0){
            $data['pormotion_num'] = $request->param('pormotion','','string');
            if(!is_int($data['pormotion_num']))
                return $this->returnJson('晋级队伍为整数');
            if(empty($data['pormotion_num']))
                return $this->returnJson('晋级队伍不能为0');
        }
        if(empty($data['name']) or empty($data['address']) or empty($data['start_time']) or empty($data['end_time']))
            return $this->returnJson('赛事名、地区、开始、结束时间不能为空');
        $eventModel = Db::name('event');
        $nameExist = $eventModel->where(array('name'=>$data['name']))->find();
        if(!empty($nameExist))
            return $this->returnJson('赛事名称已存在！');
        if($data['start_time']>$data['end_time'])
            return $this->returnJson('结束时间不能小于开始时间');
        if($data['start_time']<time()+3600*24)
            return $this->returnJson('开始时间不能小于当前时间一天');
        if(!empty($data['posters']) and !file_exists(PUBLIC_PATH.$data['posters']))
            return $this->returnJson('上传海报有错误');
        $data['start_time'] = date('Y-m-d',$data['start_time']);
        $data['end_time'] = date('Y-m-d',$data['end_time']);
        $strings = $string = join('',array_merge(range(0,9),range('a','z'),range('A','Z')));
        $virefy = '';
        for($i=0;$i<6;$i++)
            $virefy .= str_shuffle($strings){0};
        $data['virefy_code'] = $virefy;
        $data['join_clubs'] = '';
        $data['create_time'] = time();
        $data['create_user'] = $user['Id'];
        $res = $eventModel->insert($data);
        if(!$res)
            return $this->returnJson('添加赛事失败,请重试');
        return $this->returnJson('添加赛事成功,请等待审核....',true,1);
    }

    //取消赛事
    public function cancel(){

    }

    //不通过重新申请
    public function again(){

    }

    //上传海报
    public function posters(){
        //防止恶意上传操作
        $user = Session::get('user')[0];
        $uploadTimes = (int)Cache::get(__CLASS__.__FUNCTION__.$user['Id']);
        if($uploadTimes === false){
            Cache::set(__CLASS__.__FUNCTION__.$user['Id'],1,3600*2);
        }else{
            Cache::set(__CLASS__.__FUNCTION__.$user['Id'],$uploadTimes+1,3600*2);
        }
        if($uploadTimes >10){
            return $this->returnJson('多次操作被限制');
        }
        $upload = new Upload();
        $upload->setFormName('postersFile');
        $result = $upload->exec();
        if(!$result){
            return $this->returnJson('文件未上传');
        }
        $path = $upload->path('/uploads/event/posterss/');
        $upload->buildCode();
        $code = $upload->getRetval();
        $fileName = $path.$code['code'].'.'.$upload->getFileSuffix();
        $result = $upload->moveFile($fileName);
        if(!$result){
            return $this->returnJson('文件上传失败');
        }
        $res = Tool::uploadImage($fileName,$fileName);
        if(!$res){
            return $this->returnJson('图片重生成错误');
        }
        $fileName = str_replace(PUBLIC_PATH,'',$fileName);
        return $this->returnJson('上传成功',true,1,array('fileName'=>$fileName));
    }

    //我的赛事管理
    public function management(){
        $id = $this->request->param('id','','int');
        $user = $this->getUser();
        $event = Db::name('event')->where(['Id'=>$id,'create_user'=>$user['Id']])->find();
        if(empty($event))
            return $this->fetch(APP_PATH.'index/view/error.phtml',['error'=>'您没有该赛事的管理权限']);
        $this->assign('event',$event);
        $joins = json_decode($event['join_clubs'],true);
        $joinClubs = [];
        $clubModel = Db::name('club');
        foreach ($joins as $key=>$join){
            $club = $clubModel->where('Id',$key)->find();
            $joinClubs[] = $club;
        }
        $this->assign('joins',$joinClubs);
        $schedules = Db::name('schedule')
            ->where('event_id',$id)
            ->order(['over','game_time'=>'desc'])
            ->select();
        $this->assign('schedules',$schedules);
        $types = Config::get('basketball.event_types');
        $this->assign('types',$types);
        return $this->fetch();

    }

    //修改邀请码
    public function alterCode(){
        $id = $this->request->param('id','','int');
        $user = $this->getUser();
        $event = Db::name('event')->where(['Id'=>$id,'create_user'=>$user['Id']])->find();
        if(empty($event))
            return $this->returnJson('您没有权限修改该赛事邀请码');
        $code = $this->request->param('code','','string');
        if(strlen($code)<4 or strlen($code)>8)
            return $this->returnJson('邀请码的长度在4-8长度之内');
        $update = ['Id'=>$id,'virefy_code'=>$code];
        $res = Db::name('event')->update($update);
        if(!$res)
            return $this->returnJson('修改失败，请重试');
        return $this->returnJson('修改成功',true,1);
    }

    public function alterTime(){
        $id = $this->request->param('id','','int');
        $event = Db::name('event')->where('Id',$id)->find();
        if(empty($event))
            return $this->returnJson('赛事不存在');
        $user= $this->getUser();
        if($event['create_user']!=$user['Id'])
            return $this->returnJson('您没有操作权限');
        $start = $this->request->param('startTime','','string');
        $end =  $this->request->param('endTime','','string');
        $start = date('Y-m-d',strtotime($start));
        $end = date('Y-m-d',strtotime($end));
        if($start>$end)
            return $this->returnJson('开始时间不能大于结束时间');
        $update = ['Id'=>$id,'start_time'=>$start,'end_time'=>$end];
        $res = Db::name('event')->update($update);
        if(!$res)
            return $this->returnJson('更改失败请重试');
        return $this->returnJson('更改成功',true,1);
    }

    //踢出比赛队伍
    public function delJoin(){
        $id = $this->request->param('id','','int');
        $clubId = $this->request->param('clubId','','int');
        $user = $this->getUser();
        $event = Db::name('event')->where(['Id'=>$id,'create_user'=>$user['Id']])->find();
        if(empty($event))
            return $this->returnJson('您没有权限踢出队伍');
        $joins = json_decode($event['join_clubs'],true);
        if(!isset($joins[$clubId]))
            return $this->returnJson('踢出的队伍不存在');
        unset($joins[$clubId]);
        $update = ['Id'=>$id,'join_clubs'=>json_encode($joins)];
        $res = Db::name('event')->update($update);
        if(!$res)
            return $this->returnJson('踢出失败，请重试');
        return $this->returnJson('踢出成功',true,1);
    }

}