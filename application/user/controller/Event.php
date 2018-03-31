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
        $user  = Session::get('user');
        $user = isset($user[0])?$user[0]:$user;
        $eventModel = Db::name('event');
        $where[ 'create_user'] = $user['Id'];
        $events = $eventModel->where($where)
            ->order('create_time desc')
            ->paginate(10,false,array('page'=>$page))
            ->toArray();
        $this->assign('events',$events);
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
        $data['name'] = $request->param('name','','string');
        $data['type'] = (int)$request->param('type','','int');
        $data['address'] = $request->param('address','','string');
        $data['posters'] = $request->param('posters','','string');
        $data['start_time'] = strtotime($request->param('startTime','','string'));
        $data['end_time'] = strtotime($request->param('endTime','','string'));
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
            return $this->returnJson('上传海拔有错误');
        $data['start_time'] = date('Y-m-d',$data['start_time']);
        $data['end_time'] = date('Y-m-d',$data['end_time']);
        $strings = $string = join('',array_merge(range(0,9),range('a','z'),range('A','Z')));
        $virefy = '';
        for($i=0;$i<6;$i++)
            $virefy .= str_shuffle($strings){0};
        $data['virefy_code'] = $virefy;
        $data['join_clubs'] = '';
        $data['create_time'] = time();
        $user = Session::get('user')[0];
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

}