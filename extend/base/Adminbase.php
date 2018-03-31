<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/2/14
 * Time: 13:15
 */
namespace base;
use base\Base;
use think\Session;
class Adminbase extends Base{
    protected $_ec = array(
        'login',
    );
    protected $_ac = array(
        'login'=>'*',
    );
    protected function _initialize() {
        $session = new Session();
        $admin = $session->get('admin_user');
        if($this->isFilter()===false){
            if(empty($admin)){
                $this->redirect('/admin/');
            }
        }
        $admin = isset($admin[0])?$admin[0]:null;
        $this->assign('admin',$admin);
    }

    protected function isFilter(){
        $request = $this->request;
        $controller = strtolower($request->controller());
        $action = strtolower($request->action());
        if(!in_array($controller,$this->_ec)){
            return false;
        }
        if($this->_ac[$controller]== '*'){
            return true;
        }elseif($this->_ac[$controller]==$action){
            return true;
        }else{
            return false;
        }
    }


}