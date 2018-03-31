<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/9
 * Time: 11:48
 */
namespace app\admin\controller;

use base\Adminbase;
use think\Db;
use think\Config;
use ku\Email;
use ku\Verify;;

class Event extends Adminbase{

    protected $_error = '';

    public function index(){
        $eventModel = Db::name('event');
        $where = array();
        $where['audit'] = array('in','0,3');
        $request = $this->request;
        $page = $request->param('page',1,'int');
        $name = $request->param('name','','string');
        if($name){
            $where['name'] = array('like','%'.$name.'%');
        }
        $this->assign('name',$name);
        $events = $eventModel->where($where)
            ->order('create_time asc')
            ->paginate(10,false,array('page'=>$page))
            ->toArray();
        $this->assign('pager',$events);
        $eventTypes = Config::get('basketball.event_types');
        $this->assign('types',$eventTypes);
        $eventStatus = Config::get('basketball.event_status');
        $this->assign('eventStatus',$eventStatus);
        return $this->fetch();
    }

    public function audit(){
        $request = $this->request;
        $id = $request->param('id',0,'int');
        $eventModel = Db::name('event');
        $event = $eventModel->where(array('Id'=>$id))->find();
        if(empty($event)){
            return $this->returnJson('赛事不存在');
        }
        $audit = (int)$request->param('audit',0,'int');
        if($audit!==1 and $audit!==2){
            return $this->returnJson('审核形式不正确');
        }
        if($event['audit'] == 3 and $audit===2){
            $audit = 4;
        }
        $data =array('Id'=>$event['Id'],'audit'=>$audit);
        $res = $eventModel->update($data);
        if($res){
            $emailRes = $this->auditEmail($event,$audit);
            if($emailRes === false){
                return $this->returnJson($this->getError());
            }
            return $this->returnJson('审核成功',true,1);
        }
        return $this->returnJson('审核失败,请重试');

    }

    public function data(){
        $eventId = $this->request->param('eventId',0,'int');
        $event = Db::name('event')->where(array('Id'=>$eventId))->find();
        if(empty($event)){
            return $this->returnJson('赛事不存在');
        }
        $eventTypes = Config::get('basketball.event_types');
        $event['type'] = $eventTypes[$event['type']];
        return $this->returnJson('获取成功',true,1,$event);
    }

    protected function setError($error){
        $this->_error = $error;
        return $this;
    }

    protected function getError(){
        return $this->_error;
    }

    //赛事审核结果邮箱发送
    public function auditEmail($event,$audit){
        $user = Db::name('user')->where(array('Id'=>$event['create_user']))->find();
        if(empty($user)){
            $this->setError('用户不存在');
            return false;
        }
        if(!Verify::isEmail($user['email'])){
            $this->setError('邮箱不正确');
            return false;
        }
        $subject = '来战吧篮球';
        $msg = '您发起的赛事——'.$event['name'];
        $msg .= $audit==1?'审核已通过':'未能通过审核，请核实后再申请';
        $body = $msg;
        $res = Email::sendEmail($user['email'],$subject,$body);
        if(!$res) {
            $this->setError('发送失败');
            return false;
        }
        return true;
    }

}