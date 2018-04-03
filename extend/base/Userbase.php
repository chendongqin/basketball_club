<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/2/14
 * Time: 13:15
 */
namespace base;
use think\Session;
class Userbase extends Base {
    protected $_ec = array(
//        'user'=>array( 'index'),
    );
    protected $_ac = array(
//        'index'=>'*',
    );
    private  $_user = '';
    protected function _initialize() {
        $session = new Session();
        $user = $session->get('user');
        if($this->isFilter()===false){
            if(empty($user)){
                return $this->redirect('/user/login');
            }
        }
        $user = isset($user[0])?$user[0]:$user;
        $this->assign('user',$user);
        $this->setUser($user);
    }

    protected function isFilter(){
        $request = $this->request;
        $module = strtolower($request->module());
        $controller = strtolower($request->controller());
        $action = strtolower($request->action());
        if(!isset($this->_ec[$module])){
            return false;
        }
        if(!in_array($controller,$this->_ec[$module])){
            return false;
        }
        if($this->_ac[$controller]== '*'){
            return true;
        }elseif(is_array($this->_ac[$controller]) and in_array($action,$this->_ac[$controller])){
            return true;
        }else{
            return false;
        }
    }

    private function setUser($user){
        $this->_user = $user;
        return $this;
    }

    protected function getUser(){
        return $this->_user;
    }

}